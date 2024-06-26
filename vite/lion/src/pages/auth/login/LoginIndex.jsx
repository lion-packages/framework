import axios from "axios";
import { Fragment, useContext, useState } from "react";
import { Button, Col, Container, Form, Row } from "react-bootstrap";
import VerifiedUser from "./components/VerifiedUser";
import { Link, useNavigate } from "react-router-dom";
import useApiResponse from "../../../hooks/useApiResponse";
import useAES from "../../../hooks/useAES";
import { AuthContext } from "../../../context/AuthContext";
import { ResponseContext } from "../../../context/ResponseContext";

export default function LoginIndex() {
  const navigate = useNavigate();
  const { getResponseFromRules } = useApiResponse();
  const { login } = useContext(AuthContext);
  const { addToast } = useContext(ResponseContext);
  const { encode } = useAES();

  const [users_email, setUsers_email] = useState("root@dev.com");
  const [users_password, setUsers_password] = useState("lion");
  const [verified, setVerified] = useState(false);

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      users_email: users_email,
      users_password: encode(users_password),
    };

    axios
      .post(`${import.meta.env.VITE_SERVER_URL_AUD}/api/auth/login`, form)
      .then(({ data }) => {
        // console.log(data);

        if ("success" === data.status) {
          login(data.data.jwt_access, data.data.jwt_refresh);

          if (null != data.data.full_name) {
            addToast([
              {
                status: data.status,
                title: "Authentication",
                message: data.message,
              },
              // {
              //   status: "info",
              //   title: "Authentication",
              //   message: `Welcome: ${data.data.full_name}`,
              // },
            ]);
          } else {
            addToast([
              {
                status: data.status,
                title: "Authentication",
                message: data.message,
              },
            ]);
          }

          navigate("/dashboard");
        } else {
          addToast([
            {
              status: "info",
              title: "Authentication",
              message: data.message,
            },
          ]);
        }
      })
      .catch((err) => {
        console.log(err);

        if (err.response && 403 === err.response.data.code) {
          setVerified(true);

          addToast([
            {
              status: err.response.data.status,
              title: "Authentication",
              message: err.response.data.message,
            },
          ]);
        }

        if (err.response && 401 === err.response.data.code) {
          addToast([
            {
              status: err.response.data.status,
              title: "Authentication",
              message: err.response.data.message,
            },
          ]);
        }

        if (err.response && 500 === err.response.data.code) {
          addToast([
            ...getResponseFromRules("Authentication", err.response.data),
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
          {verified ? (
            <VerifiedUser users_email={users_email} setVerified={setVerified} />
          ) : (
            <Fragment>
              <h4>Login</h4>

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

                <Form.Group
                  as={Row}
                  className="mb-3"
                  controlId="users_password"
                >
                  <Form.Label column sm={3}>
                    Password
                  </Form.Label>

                  <Col sm={9}>
                    <Form.Control
                      value={users_password}
                      onChange={(e) => setUsers_password(e.target.value)}
                      type="password"
                      placeholder="Password..."
                      autoComplete="off"
                    />
                  </Col>
                </Form.Group>

                <div className="d-grid gap-2 d-md-flex justify-content-md-end">
                  <Button type="submit" variant="success">
                    Login
                  </Button>
                </div>

                <hr />

                <div className="d-grid gap-2 d-md-flex justify-content-md-end">
                  <Link
                    to="/auth/recovery-password"
                    className="btn btn-link  me-2"
                  >
                    Forgot your password?
                  </Link>

                  <Link to="/auth/register" className="btn btn-link">
                    Create Account
                  </Link>
                </div>
              </Form>
            </Fragment>
          )}
        </Col>
      </Row>
    </Container>
  );
}
