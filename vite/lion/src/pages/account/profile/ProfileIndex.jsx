import axios from "axios";
import { useEffect, useState } from "react";
import { Button, Col, Container, Form, Row } from "react-bootstrap";
import { useAuth } from "../../../context/AuthProvider";
import { useResponse } from "../../../context/ResponseProvider";
import useApiResponse from "../../../hooks/useApiResponse";

export default function ProfileIndex() {
  const { getJWT } = useAuth();
  const { addToast } = useResponse();
  const { getResponseFromRules } = useApiResponse();

  const [idroles, setIdroles] = useState("");
  const [iddocument_types, setIddocument_types] = useState("");
  const [users_citizen_identification, setUsers_citizen_identification] =
    useState("");
  const [users_name, setUsers_name] = useState("");
  const [users_last_name, setUsers_last_name] = useState("");
  const [users_nickname, setUsers_nickname] = useState("");
  const [users_email, setUsers_email] = useState("");

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      iddocument_types: parseInt(iddocument_types),
      users_citizen_identification: users_citizen_identification,
      users_name: users_name,
      users_last_name: users_last_name,
      users_nickname: users_nickname,
    };

    axios
      .put(`${import.meta.env.VITE_SERVER_URL_AUD}/api/profile`, form, {
        headers: {
          Authorization: `Bearer ${getJWT()}`,
        },
      })
      .then(({ data }) => {
        // console.log(data);

        addToast([
          {
            status: data.status,
            title: "Profile",
            message: data.message,
          },
        ]);
      })
      .catch(({ response }) => {
        // console.log(response);

        addToast([...getResponseFromRules("Profile", response.data)]);
      });
  };

  const handleReadProfile = () => {
    axios
      .get(`${import.meta.env.VITE_SERVER_URL_AUD}/api/profile`, {
        headers: {
          Authorization: `Bearer ${getJWT()}`,
        },
      })
      .then(({ data }) => {
        // console.log(data);

        setIdroles(data.idroles);
        setIddocument_types(data.iddocument_types);
        setUsers_citizen_identification(data.users_citizen_identification);
        setUsers_name(data.users_name);
        setUsers_last_name(data.users_last_name);
        setUsers_nickname(data.users_nickname);
        setUsers_email(data.users_email);
      })
      .catch((err) => {
        console.log(err);
      });
  };

  useEffect(() => {
    handleReadProfile();
  }, []);

  return (
    <Container>
      <div className="my-5">
        <Form onSubmit={handleSubmit}>
          <div className="mb-3">
            <div className="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
              <h4>Account information</h4>

              {/* <div className="btn-toolbar mb-2 mb-md-0">
                <div className="btn-group">
                  <Button type="submit" variant="outline-secondary">
                    Save
                  </Button>
                </div>
              </div> */}
            </div>

            <Row>
              <Col xs={12} sm={12} md={6} lg={6} xl={4}>
                <Form.Group as={Row} className="mb-3" controlId="idroles">
                  <Form.Label column sm={3} md={4}>
                    Rol
                  </Form.Label>

                  <Col sm={9} md={8}>
                    <Form.Select
                      value={idroles}
                      onChange={(e) => setIdroles(e.target.value)}
                      aria-label="idroles"
                      disabled
                    >
                      <option value={""}>Select</option>
                      <option value="1">Administrator</option>
                      <option value="2">Manager</option>
                      <option value="3">Customer</option>
                    </Form.Select>
                  </Col>
                </Form.Group>
              </Col>

              <Col xs={12} sm={12} md={6} lg={6} xl={4}>
                <Form.Group as={Row} className="mb-3" controlId="users_email">
                  <Form.Label column sm={3} md={4}>
                    Email
                  </Form.Label>

                  <Col sm={9} md={8}>
                    <Form.Control
                      type="email"
                      value={users_email}
                      onChange={(e) => setUsers_email(e.target.value)}
                      disabled
                      placeholder="Email..."
                    />
                  </Col>
                </Form.Group>
              </Col>
            </Row>
          </div>

          <div className="mb-3">
            <div className="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
              <h4>Personal information</h4>

              <div className="btn-toolbar mb-2 mb-md-0">
                <div className="btn-group">
                  <Button type="submit" variant="light">
                    <i className="bi bi-floppy-fill"></i>
                  </Button>
                </div>
              </div>
            </div>

            <Row>
              <Col xs={12} sm={12} md={6} lg={6} xl={4}>
                <Form.Group as={Row} className="mb-3" controlId="users_name">
                  <Form.Label column sm={3} md={4}>
                    Name
                  </Form.Label>

                  <Col sm={9} md={8}>
                    <Form.Control
                      type="text"
                      value={users_name}
                      onChange={(e) => setUsers_name(e.target.value)}
                      required
                      autoComplete="off"
                      placeholder="Name..."
                    />
                  </Col>
                </Form.Group>
              </Col>

              <Col xs={12} sm={12} md={6} lg={6} xl={4}>
                <Form.Group
                  as={Row}
                  className="mb-3"
                  controlId="users_last_name"
                >
                  <Form.Label column sm={3} md={4}>
                    Last name
                  </Form.Label>

                  <Col sm={9} md={8}>
                    <Form.Control
                      type="text"
                      value={users_last_name}
                      onChange={(e) => setUsers_last_name(e.target.value)}
                      required
                      autoComplete="off"
                      placeholder="Last name..."
                    />
                  </Col>
                </Form.Group>
              </Col>

              <Col xs={12} sm={12} md={6} lg={6} xl={4}>
                <Form.Group
                  as={Row}
                  className="mb-3"
                  controlId="users_nickname"
                >
                  <Form.Label column sm={3} md={4}>
                    Nickname
                  </Form.Label>

                  <Col sm={9} md={8}>
                    <Form.Control
                      type="text"
                      value={users_nickname}
                      onChange={(e) => setUsers_nickname(e.target.value)}
                      required
                      autoComplete="off"
                      placeholder="Nickname..."
                    />
                  </Col>
                </Form.Group>
              </Col>

              <Col xs={12} sm={12} md={6} lg={6} xl={4}>
                <Form.Group
                  as={Row}
                  className="mb-3"
                  controlId="iddocument_types"
                >
                  <Form.Label column sm={3} md={4}>
                    ID Type
                  </Form.Label>

                  <Col sm={9} md={8}>
                    <Form.Select
                      required
                      value={iddocument_types}
                      onChange={(e) => setIddocument_types(e.target.value)}
                      aria-label="iddocument_types"
                    >
                      <option value={""}>Select</option>
                      <option value="1">Citizenship Card</option>
                      <option value="2">Passport</option>
                    </Form.Select>
                  </Col>
                </Form.Group>
              </Col>

              <Col xs={12} sm={12} md={6} lg={6} xl={4}>
                <Form.Group
                  as={Row}
                  className="mb-3"
                  controlId="users_citizen_identification"
                >
                  <Form.Label column sm={3} md={4}>
                    ID
                  </Form.Label>

                  <Col sm={9} md={8}>
                    <Form.Control
                      type="text"
                      value={users_citizen_identification}
                      onChange={(e) =>
                        setUsers_citizen_identification(e.target.value)
                      }
                      required
                      autoComplete="off"
                      placeholder="ID..."
                    />
                  </Col>
                </Form.Group>
              </Col>
            </Row>
          </div>
        </Form>
      </div>
    </Container>
  );
}
