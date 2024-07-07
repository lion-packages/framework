/* eslint-disable react/no-unescaped-entities */
/* eslint-disable react/prop-types */
import { Button, Col, Form, Image, Modal, Row } from "react-bootstrap";
import axiosApi from "../../../../Api";
import { useContext, useEffect, useState } from "react";
import GoogleAuthenticatorImg from "../../../../assets/img/google-authenticator.png";
import useAES from "../../../../hooks/useAES";
import { ResponseContext } from "../../../../context/ResponseContext";

export default function Enable2FAModal({ show, setShow }) {
  const { decode } = useAES();
  const { addToast } = useContext(ResponseContext);

  const [img, setImg] = useState("");
  const [secret, setSecret] = useState("");

  const handleSubmit = (event) => {
    event.preventDefault();

    console.log("subitted!!");
  };

  const handle2FA = async () => {
    try {
      const res = await axiosApi().get("/api/profile/2fa/qr");

      console.log(res);

      if (200 === res.data.code) {
        setImg(decode(res.data.data.qr));

        setSecret(decode(res.data.data.secret));
      }
    } catch (exception) {
      addToast([
        {
          status: exception.response.data.status,
          title: "Enable 2FA",
          message: exception.response.data.message,
        },
      ]);
    }
  };

  useEffect(() => {
    if (show) {
      handle2FA();
    }
  }, [show]);

  return (
    <Modal show={show} size="lg" onHide={() => setShow(false)}>
      <Modal.Header closeButton className="bg-info">
        <Modal.Title>Enable 2FA</Modal.Title>
      </Modal.Header>

      <Modal.Body>
        <Form onSubmit={handleSubmit} id="form-enable-2fa">
          <Row>
            <Col xs={12} sm={4} lg={3}>
              <div className="text-center">
                <Image src={GoogleAuthenticatorImg} fluid />
              </div>
            </Col>

            <Col xs={12} sm={8} lg={9}>
              <h5>DOWNLOAD AN AUTHENTICATOR APP</h5>

              <p>
                Download an authenticator app <strong>Authy</strong> or{" "}
                <strong>Google Authenticator</strong> for your phone or tablet.
              </p>
            </Col>
          </Row>

          <hr />

          <Row>
            <Col xs={12} sm={4} lg={3}>
              <div className="text-center">
                {img && <Image src={img} alt="QR Code" fluid />}

                <Button
                  type="button"
                  size="sm"
                  variant="secondary"
                  onClick={() => handle2FA()}
                >
                  Reload
                </Button>
              </div>
            </Col>

            <Col xs={12} sm={8} lg={9}>
              <h5>SCAN THE QR CODE</h5>

              <p>
                Open the authentication app and scan the image to the left using
                your phone's camera.
              </p>

              <h6>2FA KEY (MANUAL ENTRY)</h6>

              {secret && <Form.Control type="text" value={secret} disabled />}
            </Col>
          </Row>

          <hr />

          <Form onSubmit={handleSubmit} id="form-enable-2fa">
            <Form.Group>
              <Form.Label>
                <strong>LOG IN WITH YOUR CODE</strong>
              </Form.Label>

              <p>Enter the 6-digit verification code generated.</p>

              <Row>
                <Col xs={12} sm={7} md={4}>
                  <Form.Control
                    type="number"
                    min={100000}
                    max={999999}
                    required
                  />
                </Col>
              </Row>
            </Form.Group>
          </Form>
        </Form>
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
