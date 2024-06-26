import axios from "axios";
import { useContext, useState } from "react";
import { Button, Col, Container, Form, Row } from "react-bootstrap";
import { Link, useNavigate } from "react-router-dom";
import useApiResponse from "../../../hooks/useApiResponse";
import useAES from "../../../hooks/useAES";
import { ResponseContext } from "../../../context/ResponseContext";

export default function RegisterIndex() {
  const navigate = useNavigate();
  const { addToast } = useContext(ResponseContext);
  const { getResponseFromRules } = useApiResponse();
  const { encode } = useAES();

  const [users_email, setUsers_email] = useState("");
  const [users_password, setUsers_password] = useState("");

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      users_email: users_email,
      users_password: encode(users_password),
    };

    axios
      .post(`${import.meta.env.VITE_SERVER_URL_AUD}/api/auth/register`, form)
      .then(({ data }) => {
        // console.log(data);

        addToast([
          {
            status: data.status,
            title: "Registration",
            message: data.message,
          },
        ]);

        if (data.status === "success") {
          navigate("/auth/login");
        }
      })
      .catch((err) => {
        // console.log(response);

        if (err.response && 400 === err.response.data.code) {
          addToast([
            {
              status: err.response.data.status,
              title: "Registration",
              message: err.response.data.message,
            },
          ]);
        }

        if (err.response && 500 === err.response.data.code) {
          addToast([
            ...getResponseFromRules("Registration", err.response.data),
            {
              status: err.response.data.status,
              title: "Registration",
              message: err.response.data.message,
            },
          ]);
        }
      });
  };

  return (
    <Container>
      <Row>
        <Col
          xs={12}
          sm={12}
          md={8}
          lg={7}
          xl={5}
          xxl={5}
          className="mx-auto my-5 bg-light border rounded p-3"
        >
          <h4>Register</h4>

          <hr />

          <Form onSubmit={handleSubmit}>
            <Form.Group as={Row} className="mb-3" controlId="users_email">
              <Form.Label column sm={3}>
                Email
              </Form.Label>

              <Col sm={9}>
                <Form.Control
                  value={users_email}
                  onChange={(e) => setUsers_email(e.target.value)}
                  type="email"
                  placeholder="Email..."
                  required
                  autoComplete="off"
                />
              </Col>
            </Form.Group>

            <Form.Group as={Row} className="mb-3" controlId="users_password">
              <Form.Label column sm={3}>
                Password
              </Form.Label>

              <Col sm={9}>
                <Form.Control
                  value={users_password}
                  onChange={(e) => setUsers_password(e.target.value)}
                  type="password"
                  placeholder="Password..."
                  required
                  autoComplete="off"
                />
              </Col>
            </Form.Group>

            <Button type="submit" variant="success" className="float-end">
              Register
            </Button>

            <Link to="/auth/login" className="btn btn-link float-end me-2">
              Login
            </Link>
          </Form>
        </Col>
      </Row>
    </Container>
  );
}
