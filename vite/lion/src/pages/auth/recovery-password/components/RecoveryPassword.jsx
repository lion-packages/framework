/* eslint-disable react/prop-types */
import axios from "axios";
import { Fragment, useState } from "react";
import { Button, Col, Form, Row } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import { useResponse } from "../../../../context/ResponseProvider";
import useApiResponse from "../../../../hooks/useApiResponse";
import useAES from "../../../../hooks/useAES";

export default function RecoveryPassword({ users_email, setActive }) {
  const navigate = useNavigate();
  const { addToast } = useResponse();
  const { getResponseFromRules } = useApiResponse();
  const { encode } = useAES();

  const [codes, setCodes] = useState(["", "", "", "", "", ""]);
  const [users_password_new, setUsers_password_new] = useState("");
  const [users_password_confirm, setUsers_password_confirm] = useState("");
  const inputs = [];

  const handleChange = (e, index) => {
    const { value } = e.target;

    if (value.length > 1) {
      return;
    }

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
      users_password_new: encode(users_password_new),
      users_password_confirm: encode(users_password_confirm),
      users_recovery_code: codes.join("").trim(),
    };

    axios
      .post(
        `${import.meta.env.VITE_SERVER_URL_AUD}/api/auth/recovery/verify-code`,
        form
      )
      .then(({ data }) => {
        addToast([
          {
            status: data.status,
            title: "Recover password",
            message: data.message,
          },
        ]);

        if ("success" === data.status) {
          setActive(false);

          navigate("/auth/login");
        }
      })
      .catch(({ response }) => {
        if (403 === response.data.code || 401 === response.data.code) {
          addToast([
            {
              status: response.data.status,
              title: "Recover password",
              message: response.data.message,
            },
          ]);
        }

        if (500 === response.data.code) {
          addToast([
            ...getResponseFromRules("Recover password", response.data),
          ]);
        }
      });
  };

  return (
    <Fragment>
      <h4>Recovery code</h4>

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

        <Form.Group as={Row} className="mb-3" controlId="users_password_new">
          <Form.Label column sm={3}>
            New password
          </Form.Label>

          <Col sm={9}>
            <Form.Control
              value={users_password_new}
              onChange={(e) => setUsers_password_new(e.target.value)}
              type="password"
              placeholder="New password..."
              autoComplete="off"
            />
          </Col>
        </Form.Group>

        <Form.Group
          as={Row}
          className="mb-3"
          controlId="users_password_confirm"
        >
          <Form.Label column sm={3}>
            Confirm new password
          </Form.Label>

          <Col sm={9}>
            <Form.Control
              value={users_password_confirm}
              onChange={(e) => setUsers_password_confirm(e.target.value)}
              type="password"
              placeholder="Confirm new password..."
              autoComplete="off"
            />
          </Col>
        </Form.Group>

        <hr />

        <div className="d-grid gap-2 d-md-flex justify-content-md-end">
          <Button type="submit" variant="primary">
            Verify
          </Button>
        </div>
      </Form>
    </Fragment>
  );
}
