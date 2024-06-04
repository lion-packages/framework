import axios from "axios";
import { Fragment, useContext, useState } from "react";
import { Button, Col, Container, Form, Row } from "react-bootstrap";
import { Link } from "react-router-dom";
import RecoveryPassword from "./components/RecoveryPassword";
import useApiResponse from "../../../hooks/useApiResponse";
import { ResponseContext } from "../../../context/ResponseContext";

export default function RecoveryPasswordIndex() {
  const { addToast } = useContext(ResponseContext);
  const { getResponseFromRules } = useApiResponse();

  const [users_email, setUsers_email] = useState("root@dev.com");
  const [active, setActive] = useState(false);

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      users_email: users_email,
    };

    axios
      .post(
        `${import.meta.env.VITE_SERVER_URL_AUD}/api/auth/recovery/password`,
        form
      )
      .then(({ data }) => {
        addToast([
          {
            status: data.status,
            title: "Recover password",
            message: data.message,
          },
        ]);

        if ("success" === data.status) {
          setActive(true);
        }
      })
      .catch(({ response }) => {
        if (403 === response.data.code) {
          setActive(true);
        }

        if (500 === response.data.code) {
          addToast([
            ...getResponseFromRules("Recover password", response.data),
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
          md={10}
          lg={7}
          xl={5}
          xxl={5}
          className="mx-auto my-5 bg-light border rounded p-3"
        >
          {active ? (
            <RecoveryPassword users_email={users_email} setActive={setActive} />
          ) : (
            <Fragment>
              <h4>Recover password</h4>

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

                <Button type="submit" variant="success" className="float-end">
                  Send
                </Button>

                <Link to="/auth/login" className="btn btn-link float-end me-2">
                  Return
                </Link>
              </Form>
            </Fragment>
          )}
        </Col>
      </Row>
    </Container>
  );
}
