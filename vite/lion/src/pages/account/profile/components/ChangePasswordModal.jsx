/* eslint-disable react/prop-types */
import { Fragment, useContext, useEffect, useState } from "react";
import { Button, Form, Modal } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import { AuthContext } from "../../../../context/AuthContext";
import { ResponseContext } from "../../../../context/ResponseContext";
import useApiResponse from "../../../../hooks/useApiResponse";
import useAES from "../../../../hooks/useAES";
import axiosApi from "../../../../Api";

export default function ChangePasswordModal({ show, setShow }) {
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

    try {
      const res = await axiosApi(refreshToken).post(
        `/api/profile/password`,
        form
      );

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
    } catch (exception) {
      if (401 === exception.response.data.code) {
        addToast([
          {
            status: exception.response.data.status,
            title: "Change Password",
            message: exception.response.data.message,
          },
        ]);
      }

      if (500 === exception.response.data.code) {
        addToast([
          ...getResponseFromRules("Change Password", exception.response.data),
        ]);
      }
    }
  };

  const FooterButtons = () => {
    return (
      <Fragment>
        <Button variant="secondary" onClick={() => setShow(false)}>
          Close
        </Button>

        <Button type="submit" variant="warning" form="form-change-password">
          Change Password
        </Button>
      </Fragment>
    );
  };

  useEffect(() => {
    setUsers_password("");

    setUsers_password_new("");

    setUsers_password_confirm("");
  }, []);

  return (
    <Modal show={show} onHide={() => setShow(false)}>
      <Modal.Header closeButton className="bg-warning">
        <Modal.Title>Change Password</Modal.Title>
      </Modal.Header>

      <Modal.Body>
        <Form onSubmit={handleSubmit} id="form-change-password">
          <Form.Group className="mb-3" controlId="users_password">
            <Form.Label>Current Password</Form.Label>

            <Form.Control
              value={users_password}
              onChange={(e) => setUsers_password(e.target.value)}
              type="password"
              placeholder="Current Password..."
              autoComplete="off"
              required
            />
          </Form.Group>

          <Form.Group className="mb-3" controlId="users_password_new">
            <Form.Label>New password</Form.Label>

            <Form.Control
              value={users_password_new}
              onChange={(e) => setUsers_password_new(e.target.value)}
              type="password"
              placeholder="New password..."
              autoComplete="off"
              required
            />
          </Form.Group>

          <Form.Group className="mb-3" controlId="users_password_confirm">
            <Form.Label>Confirm new password</Form.Label>

            <Form.Control
              value={users_password_confirm}
              onChange={(e) => setUsers_password_confirm(e.target.value)}
              type="password"
              placeholder="Confirm new password..."
              autoComplete="off"
              required
            />
          </Form.Group>
        </Form>
      </Modal.Body>

      <Modal.Footer>
        <FooterButtons />
      </Modal.Footer>
    </Modal>
  );
}
