/* eslint-disable react/prop-types */
import { Button, Form, Modal } from "react-bootstrap";

export default function Enable2FAModal({ show, setShow }) {
  const handleSubmit = (event) => {
    event.preventDefault();

    console.log("subitted!!");
  };

  return (
    <Modal show={show} onHide={() => setShow(false)}>
      <Modal.Header closeButton className="bg-info">
        <Modal.Title>Enable 2FA</Modal.Title>
      </Modal.Header>

      <Modal.Body>
        <Form onSubmit={handleSubmit} id="form-enable-2fa"></Form>
      </Modal.Body>

      <Modal.Footer>
        <Button variant="secondary" onClick={() => setShow(false)}>
          Close
        </Button>

        <Button type="submit" form="form-enable-2fa" variant="info">
          Enable 2FA
        </Button>
      </Modal.Footer>
    </Modal>
  );
}
