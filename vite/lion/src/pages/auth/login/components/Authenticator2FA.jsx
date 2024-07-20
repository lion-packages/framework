/* eslint-disable react/prop-types */
import axios from "axios";
import { Fragment, useContext, useState } from "react";
import { Button, Col, Form, Row } from "react-bootstrap";
import { AuthContext } from "../../../../context/AuthContext";
import { ResponseContext } from "../../../../context/ResponseContext";
import { useNavigate } from "react-router-dom";
import useApiResponse from "../../../../hooks/useApiResponse";

export default function Authenticator2FA({
  users_email,
  setShow,
  setAuthenticator2FA,
}) {
  const navigate = useNavigate();
  const { login } = useContext(AuthContext);
  const { addToast } = useContext(ResponseContext);
  const { getResponseFromRules } = useApiResponse();

  const [disabledButton, setDisabledButton] = useState(false);
  const [codes, setCodes] = useState(["", "", "", "", "", ""]);
  const inputs = [];

  const handleSubmit = (event) => {
    event.preventDefault();

    setDisabledButton(true);

    const form = {
      users_email: users_email,
      users_secret_code: codes.join("").trim(),
    };

    axios
      .post(`${import.meta.env.VITE_SERVER_URL_AUD}/api/auth/2fa`, form)
      .then(({ data }) => {
        // console.log(data);

        if (200 === data.code) {
          login(data.data.jwt_access, data.data.jwt_refresh, {
            auth_2fa: data.data.auth_2fa,
          });

          addToast([
            {
              status: data.status,
              title: "Authentication",
              message: data.message,
            },
          ]);

          setDisabledButton(false);

          setAuthenticator2FA(false);

          setShow(true);

          navigate("/dashboard");
        } else {
          addToast([
            {
              status: "info",
              title: "Authentication",
              message: data.message,
            },
          ]);
        }
      })
      .catch((err) => {
        // console.log(err);

        setDisabledButton(false);

        if (err.response && 403 === err.response.data.code) {
          addToast([
            {
              status: err.response.data.status,
              title: "Authentication",
              message: err.response.data.message,
            },
          ]);

          setAuthenticator2FA(false);

          setShow(true);
        }

        if (err.response && 500 === err.response.data.code) {
          addToast([
            ...getResponseFromRules("Authentication", err.response.data),
          ]);

          setAuthenticator2FA(false);

          setShow(true);
        }
      });
  };

  const handleChange = (e, index) => {
    const { value } = e.target;
    if (value.length > 1) return;

    const newCodes = [...codes];

    newCodes[index] = value;

    setCodes(newCodes);

    if (value !== "") {
      const nextInput = inputs[index + 1];

      if (nextInput) {
        nextInput.focus();
      }
    }
  };

  return (
    <Fragment>
      <h4>
        Authentication code{" "}
        <i className="bi bi-phone-fill fs-4 text-warning"></i>{" "}
      </h4>

      <hr />

      <Form onSubmit={handleSubmit}>
        <p>
          Open your two-factor authentication (TOTP) app or browser extension to
          view your authentication code.
        </p>

        <div className="mb-3">
          <Row>
            {codes.map((code, index) => (
              <Col xs={2} key={index}>
                <Form.Control
                  type="text"
                  maxLength={1}
                  value={code}
                  onChange={(e) => handleChange(e, index)}
                  ref={(input) => (inputs[index] = input)}
                  className="text-center"
                  placeholder="X"
                  required
                />
              </Col>
            ))}
          </Row>
        </div>

        <hr />

        <div className="d-grid gap-2">
          <Button type="submit" variant="warning" disabled={disabledButton}>
            Verify
          </Button>
        </div>
      </Form>
    </Fragment>
  );
}
