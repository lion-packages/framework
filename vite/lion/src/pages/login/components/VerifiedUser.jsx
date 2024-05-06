import axios from "axios";
import { Fragment, useState } from "react";
import { Button, Col, Form, Row } from "react-bootstrap";
import { useNavigate } from "react-router-dom";

export default function VerifiedUser({ users_email, setVerified }) {
  const navigate = useNavigate();

  const [code_1, setCode_1] = useState("");
  const [code_2, setCode_2] = useState("");
  const [code_3, setCode_3] = useState("");
  const [code_4, setCode_4] = useState("");
  const [code_5, setCode_5] = useState("");
  const [code_6, setCode_6] = useState("");

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      users_email: users_email,
      users_activation_code:
        code_1 + code_2 + code_3 + code_4 + code_5 + code_6,
    };

    axios
      .post(`${import.meta.env.VITE_SERVER_URL_AUD}/api/auth/verify`, form)
      .then(({ data }) => {
        console.log(data);

        if (data.status === "success") {
          navigate("/auth/login");

          setVerified(false);
        }
      })
      .catch(({ response }) => {
        console.log(response.data);
      });
  };

  return (
    <Fragment>
      <h4>Activation code</h4>

      <hr />

      <Form onSubmit={handleSubmit}>
        <div className="mb-3">
          <Row>
            <Col xs={2}>
              <Form.Control
                type="number"
                required
                value={code_1}
                onChange={(e) => setCode_1(e.target.value)}
              />
            </Col>

            <Col xs={2}>
              <Form.Control
                type="number"
                required
                value={code_2}
                onChange={(e) => setCode_2(e.target.value)}
              />
            </Col>

            <Col xs={2}>
              <Form.Control
                type="number"
                required
                value={code_3}
                onChange={(e) => setCode_3(e.target.value)}
              />
            </Col>

            <Col xs={2}>
              <Form.Control
                type="number"
                required
                value={code_4}
                onChange={(e) => setCode_4(e.target.value)}
              />
            </Col>

            <Col xs={2}>
              <Form.Control
                type="number"
                required
                value={code_5}
                onChange={(e) => setCode_5(e.target.value)}
              />
            </Col>

            <Col xs={2}>
              <Form.Control
                type="number"
                required
                value={code_6}
                onChange={(e) => setCode_6(e.target.value)}
              />
            </Col>
          </Row>
        </div>

        <hr />

        <div className="d-grid gap-2">
          <Button type="submit" variant="success">
            Verify
          </Button>
        </div>
      </Form>
    </Fragment>
  );
}
