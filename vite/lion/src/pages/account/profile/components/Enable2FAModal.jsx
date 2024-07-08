/* eslint-disable react-hooks/exhaustive-deps */
/* eslint-disable react/no-unescaped-entities */
/* eslint-disable react/prop-types */
import { Button, Col, Form, Image, Modal, Row } from "react-bootstrap";
import axiosApi from "../../../../Api";
import { useContext, useEffect, useState } from "react";
import GoogleAuthenticatorImg from "../../../../assets/img/google-authenticator.png";
import useAES from "../../../../hooks/useAES";
import { ResponseContext } from "../../../../context/ResponseContext";
import { AuthContext } from "../../../../context/AuthContext";
import { useNavigate } from "react-router-dom";

export default function Enable2FAModal({ show, setShow }) {
  const navigate = useNavigate();
  const { logout } = useContext(AuthContext);
  const { decode, encode } = useAES();
  const { addToast } = useContext(ResponseContext);

  const [img, setImg] = useState("");
  const [users_2fa_secret, setUsers_2fa_secret] = useState("");
  const [users_secret_code, setUsers_secret_code] = useState("");

  const handleEnable2FA = async (event) => {
    event.preventDefault();

    const form = {
      users_2fa_secret: encode(users_2fa_secret),
      users_secret_code: users_secret_code,
    };

    try {
      const res = await axiosApi().post("/api/profile/2fa/enable", form);

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

  const handleQR = async () => {
    try {
      const res = await axiosApi().get("/api/profile/2fa/qr");

      console.log(res);

      if (200 === res.data.code) {
        setImg(decode(res.data.data.qr));

        setUsers_2fa_secret(decode(res.data.data.secret));
      }
    } catch (exception) {
      addToast([
        {
          status: exception.response.data.status,
          title: "2FA QR Code",
          message: exception.response.data.message,
        },
      ]);
    }
  };

  useEffect(() => {
    if (show) {
      handleQR();
    }
  }, [show]);

  return (
    <Modal show={show} size="lg" onHide={() => setShow(false)}>
      <Modal.Header closeButton className="bg-info">
        <Modal.Title>Enable 2FA</Modal.Title>
      </Modal.Header>

      <Modal.Body>
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
              {img && <Image src={img} className="mb-3" alt="QR Code" fluid />}

              <Button
                type="button"
                size="sm"
                variant="secondary"
                onClick={() => handleQR()}
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

            {users_2fa_secret && (
              <Form.Control type="text" value={users_2fa_secret} disabled />
            )}
          </Col>
        </Row>

        <hr />

        <Form onSubmit={handleEnable2FA} id="form-enable-2fa">
          <Form.Group>
            <Form.Label>
              <strong>LOG IN WITH YOUR CODE</strong>
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

        <Button type="submit" form="form-enable-2fa" variant="info">
          Enable 2FA
        </Button>
      </Modal.Footer>
    </Modal>
  );
}
