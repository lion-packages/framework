/* eslint-disable react/prop-types */
import axios from "axios";
import { Fragment, useState } from "react";
import { Button, Col, Form, Row } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import useApiResponse from "../../../../hooks/useApiResponse";
import { useResponse } from "../../../../context/ResponseProvider";

export default function VerifiedUser({ users_email, setVerified }) {
  const navigate = useNavigate();
  const { addToast } = useResponse();
  const { getResponseFromRules } = useApiResponse();

  const [codes, setCodes] = useState(["", "", "", "", "", ""]);
  const inputs = [];

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

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      users_email: users_email,
      users_activation_code: codes.join("").trim(),
    };

    axios
      .post(`${import.meta.env.VITE_SERVER_URL_AUD}/api/auth/verify`, form)
      .then(({ data }) => {
        // console.log(data);

        if (data.status === "success") {
          addToast([
            {
              status: data.status,
              title: "Activation code",
              message: data.message,
            },
          ]);

          setVerified(false);

          navigate("/auth/login");
        }
      })
      .catch(({ response }) => {
        // console.log(response.data);

        if (400 === response.data.status) {
          addToast([
            {
              status: response.data.status,
              title: "Activation code",
              message: response.data.message,
            },
          ]);
        }

        if (403 === response.data.code) {
          addToast([
            {
              status: response.data.status,
              title: "Registration",
              message: response.data.message,
            },
          ]);
        }

        if (500 === response.data.status) {
          addToast([...getResponseFromRules("Activation code", response.data)]);
        }
      });
  };

  return (
    <Fragment>
      <h4>Activation code</h4>

      <hr />

      <Form onSubmit={handleSubmit}>
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
                  required
                />
              </Col>
            ))}
          </Row>
        </div>

        <hr />

        <div className="d-grid gap-2">
          <Button type="submit" variant="primary">
            Verify
          </Button>
        </div>
      </Form>
    </Fragment>
  );
}
