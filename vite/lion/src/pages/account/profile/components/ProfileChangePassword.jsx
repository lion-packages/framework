import axios from "axios";
import { useState } from "react";
import { Button, Col, Form, Row } from "react-bootstrap";
import { useAuth } from "../../../../context/AuthProvider";
import { useResponse } from "../../../../context/ResponseProvider";
import useApiResponse from "../../../../hooks/useApiResponse";
import sha256 from "crypto-js/sha256";
import { useNavigate } from "react-router-dom";

export default function ProfileChangePassword() {
  const navigate = useNavigate();
  const { getJWT, logout } = useAuth();
  const { addToast } = useResponse();
  const { getResponseFromRules } = useApiResponse();

  const [users_password, setUsers_password] = useState("");
  const [users_password_new, setUsers_password_new] = useState("");
  const [users_password_confirm, setUsers_password_confirm] = useState("");

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      users_password: sha256(users_password).toString(),
      users_password_new: sha256(users_password_new).toString(),
      users_password_confirm: sha256(users_password_confirm).toString(),
    };

    axios
      .post(
        `${import.meta.env.VITE_SERVER_URL_AUD}/api/profile/password`,
        form,
        {
          headers: {
            Authorization: `Bearer ${getJWT()}`,
          },
        }
      )
      .then(({ data }) => {
        addToast([
          {
            status: data.status,
            title: "Change Password",
            message: data.message,
          },
        ]);

        if (200 === data.code) {
          logout();

          navigate("/auth/login");
        }
      })
      .catch(({ response }) => {
        if (401 === response.data.code) {
          addToast([
            {
              status: response.data.status,
              title: "Change Password",
              message: response.data.message,
            },
          ]);
        }

        if (500 === response.data.code) {
          addToast([...getResponseFromRules("Change Password", response.data)]);
        }
      });
  };

  return (
    <Form onSubmit={handleSubmit}>
      <Row>
        <Col xs={12} sm={12} md={6} lg={6}>
          <Form.Group as={Row} className="mb-3" controlId="users_password">
            <Form.Label column sm={3}>
              Current Password
            </Form.Label>

            <Col sm={9}>
              <Form.Control
                value={users_password}
                onChange={(e) => setUsers_password(e.target.value)}
                type="password"
                placeholder="Current Password..."
                autoComplete="off"
              />
            </Col>
          </Form.Group>
        </Col>
      </Row>

      <Row>
        <Col xs={12} sm={12} md={6} lg={6}>
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
        </Col>

        <Col xs={12} sm={12} md={6} lg={6}>
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
        </Col>
      </Row>

      <Button type="submit" variant="success" className="float-end">
        Change Password
      </Button>
    </Form>
  );
}
