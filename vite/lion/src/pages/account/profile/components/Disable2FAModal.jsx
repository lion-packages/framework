/* eslint-disable react/prop-types */
import { useContext, useState } from "react";
import { Button, Col, Form, Modal, Row } from "react-bootstrap";
import axiosApi from "../../../../Api";
import { ResponseContext } from "../../../../context/ResponseContext";
import { AuthContext } from "../../../../context/AuthContext";
import { useNavigate } from "react-router-dom";

export default function Disable2FAModal({ show, setShow }) {
  const navigate = useNavigate();
  const { addToast } = useContext(ResponseContext);
  const { logout, refreshToken } = useContext(AuthContext);

  const [users_secret_code, setUsers_secret_code] = useState("");

  const handleDisable2FA = async (event) => {
    event.preventDefault();

    const form = {
      users_secret_code: users_secret_code,
    };

    try {
      const res = await axiosApi(refreshToken).post(
        "/api/profile/2fa/disable",
        form
      );

      if (200 === res.data.code) {
        setUsers_secret_code("");

        setShow(false);

        addToast([
          {
            status: "info",
            title: "Sign off",
            message: "You have been signed off",
          },
          {
            status: res.data.status,
            title: "Enable 2FA",
            message: res.data.message,
          },
        ]);

        logout();

        navigate("/auth/login");
      }
    } catch (exception) {
      if (500 === exception.response.data.code) {
        addToast([
          {
            status: exception.response.data.status,
            title: "Enable 2FA",
            message: exception.response.data.message,
          },
        ]);
      }
    }
  };

  return (
    <Modal show={show} size="lg" onHide={() => setShow(false)}>
      <Modal.Header closeButton className="bg-danger text-white">
        <Modal.Title>Disable 2FA</Modal.Title>
      </Modal.Header>

      <Modal.Body>
        <Form onSubmit={handleDisable2FA} id="form-enable-2fa">
          <Form.Group>
            <Form.Label>
              <strong>DISABLE WITH YOUR CODE</strong>
            </Form.Label>

            <p>Enter the 6-digit verification code generated.</p>

            <Row>
              <Col xs={12} sm={7} md={4}>
                <Form.Control
                  type="number"
                  value={users_secret_code}
                  onChange={(e) => setUsers_secret_code(e.target.value)}
                  required
                />
              </Col>
            </Row>
          </Form.Group>
        </Form>
      </Modal.Body>

      <Modal.Footer>
        <Button variant="secondary" onClick={() => setShow(false)}>
          Close
        </Button>

        <Button type="submit" form="form-enable-2fa" variant="danger">
          Disable 2FA
        </Button>
      </Modal.Footer>
    </Modal>
  );
}
