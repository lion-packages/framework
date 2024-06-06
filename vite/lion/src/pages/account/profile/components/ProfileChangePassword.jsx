import axios from "axios";
import { useContext, useState } from "react";
import { Button, Col, Form, Row } from "react-bootstrap";
import useApiResponse from "../../../../hooks/useApiResponse";
import { useNavigate } from "react-router-dom";
import useAES from "../../../../hooks/useAES";
import { AuthContext } from "../../../../context/AuthContext";
import { ResponseContext } from "../../../../context/ResponseContext";
import axiosApi from "../../../../Api";

export default function ProfileChangePassword() {
  const navigate = useNavigate();
  const { refreshToken, logout } = useContext(AuthContext);
  const { addToast } = useContext(ResponseContext);
  const { getResponseFromRules } = useApiResponse();
  const { encode } = useAES();

  const [users_password, setUsers_password] = useState("");
  const [users_password_new, setUsers_password_new] = useState("");
  const [users_password_confirm, setUsers_password_confirm] = useState("");

  const handleSubmit = async (event) => {
    event.preventDefault();

    const form = {
      users_password: encode(users_password),
      users_password_new: encode(users_password_new),
      users_password_confirm: encode(users_password_confirm),
    };

    const res = await axiosApi(refreshToken).post(
      `/api/profile/password`,
      form
    );

    if (res.data) {
      addToast([
        {
          status: res.data.status,
          title: "Change Password",
          message: res.data.message,
        },
      ]);

      if (200 === res.data.code) {
        logout();

        navigate("/auth/login");
      }
    }

    if (res.response) {
      if (500 === res.response.data.code) {
        addToast([
          ...getResponseFromRules("Change Password", res.response.data),
        ]);
      }
    }
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
