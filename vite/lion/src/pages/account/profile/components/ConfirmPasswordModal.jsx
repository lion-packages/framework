/* eslint-disable react/prop-types */
import { useContext, useState } from "react";
import { Button, Form, Modal } from "react-bootstrap";
import useAES from "../../../../hooks/useAES";
import axiosApi from "../../../../Api";
import { ResponseContext } from "../../../../context/ResponseContext";
import { AuthContext } from "../../../../context/AuthContext";

export default function ConfirmPasswordModal({
  show,
  setShow,
  setShow2FA,
  setShow2FADisable,
  modalType,
}) {
  const { encode } = useAES();
  const { addToast } = useContext(ResponseContext);
  const { refreshToken } = useContext(AuthContext);

  const [users_password, setUsers_password] = useState("");

  const handleSubmit = async (event) => {
    event.preventDefault();

    const form = {
      users_password: encode(users_password),
    };

    try {
      const res = await axiosApi(refreshToken).post(
        "/api/profile/2fa/verify",
        form
      );

      if (200 === res.data.code) {
        setUsers_password("");

        if (!modalType) {
          setShow2FA(true);
        } else {
          setShow2FADisable(true);
        }

        setShow(false);
      }
    } catch (exception) {
      if (500 === exception.response.data.code) {
        addToast([
          {
            status: exception.response.data.status,
            title: "Enable Authenticator App",
            message: exception.response.data.message,
          },
        ]);
      }

      setShow(false);

      setUsers_password("");
    }
  };

  return (
    <Modal show={show} onHide={() => setShow(false)}>
      <Modal.Header closeButton className="bg-info">
        <Modal.Title>Enable Authenticator App</Modal.Title>
      </Modal.Header>

      <Modal.Body>
        <Form onSubmit={handleSubmit} id="form-enable-2fa">
          <Form.Group className="mb-3" controlId="users_password">
            <Form.Label>Password</Form.Label>

            <Form.Control
              type="password"
              placeholder="Password..."
              value={users_password}
              onChange={(e) => setUsers_password(e.target.value)}
              autoComplete="off"
              required
            />
          </Form.Group>
        </Form>
      </Modal.Body>

      <Modal.Footer>
        <Button variant="secondary" onClick={() => setShow(false)}>
          Close
        </Button>

        <Button type="submit" form="form-enable-2fa" variant="info">
          Continue
        </Button>
      </Modal.Footer>
    </Modal>
  );
}
