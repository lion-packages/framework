import { Button, Col, Container, Form, Row } from "react-bootstrap";
import { useNavigate, useParams } from "react-router-dom";
import { useContext, useEffect, useState } from "react";
import axios from "axios";
import useApiResponse from "../../../../hooks/useApiResponse";
import { AuthContext } from "../../../../context/AuthContext";
import { ResponseContext } from "../../../../context/ResponseContext";

export default function UsersUpdate() {
  const navigate = useNavigate();
  const { getJWT } = useContext(AuthContext);
  const { addToast } = useContext(ResponseContext);
  const { idusers } = useParams();
  const { getResponseFromRules } = useApiResponse();

  const [idroles, setIdroles] = useState("");
  const [iddocument_types, setIddocument_types] = useState("");
  const [users_name, setUsers_name] = useState("");
  const [users_last_name, setUsers_last_name] = useState("");
  const [users_nickname, setUsers_nickname] = useState("");
  const [users_citizen_identification, setUsers_citizen_identification] =
    useState("");
  const [users_email, setUsers_email] = useState("");

  const handleReadUsersById = () => {
    axios
      .get(`${import.meta.env.VITE_SERVER_URL_AUD}/api/users/${idusers}`, {
        headers: {
          Authorization: `Bearer ${getJWT()}`,
        },
      })
      .then(({ data }) => {
        if (!data.status) {
          setIdroles(data.idroles);
          setIddocument_types(data.iddocument_types);
          setUsers_name(data.users_name);
          setUsers_last_name(data.users_last_name);
          setUsers_nickname(data.users_nickname);
          setUsers_citizen_identification(data.users_citizen_identification);
          setUsers_email(data.users_email);
        }
      })
      .catch(({ response }) => {
        console.log(response.data);
      });
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      idroles: parseInt(idroles),
      iddocument_types: parseInt(iddocument_types),
      users_name: users_name,
      users_last_name: users_last_name,
      users_nickname: users_nickname,
      users_citizen_identification: users_citizen_identification,
      users_email: users_email,
    };

    axios
      .put(
        `${import.meta.env.VITE_SERVER_URL_AUD}/api/users/${idusers}`,
        form,
        {
          headers: {
            Authorization: `Bearer ${getJWT()}`,
          },
        }
      )
      .then(({ data }) => {
        addToast([
          {
            status: data.status,
            title: "Users Update",
            message: data.message,
          },
        ]);

        if (200 === data.code) {
          navigate(`/site-administration/users`);
        }
      })
      .catch(({ response }) => {
        if (500 === response.data.code) {
          if (response.data.data["rules-error"]) {
            addToast([...getResponseFromRules("Update Users", response.data)]);
          } else {
            addToast([
              {
                status: response.data.status,
                title: "Update Users",
                message: response.data.message,
              },
            ]);
          }
        }
      });
  };

  useEffect(() => {
    handleReadUsersById();
  }, []);

  return (
    <Container>
      <div className="my-3">
        <div className="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h4>Edit user</h4>

          <div className="btn-toolbar mb-2 mb-md-0">
            <Button
              type="button"
              variant="secondary"
              size="sm"
              className="me-2"
              onClick={() => navigate(`/site-administration/users`)}
            >
              Return
            </Button>
          </div>
        </div>

        <Form onSubmit={handleSubmit}>
          <Row>
            <Col xs={12} sm={6} md={6}>
              <Form.Group as={Row} className="mb-3" controlId="idroles">
                <Form.Label column sm={3} md={4}>
                  Rol
                </Form.Label>

                <Col sm={9} md={8}>
                  <Form.Select
                    value={idroles}
                    onChange={(e) => setIdroles(e.target.value)}
                    aria-label="idroles"
                    required
                  >
                    <option value={""}>Select</option>
                    <option value={1}>Administrator</option>
                    <option value={2}>Manager</option>
                    <option value={3}>Customer</option>
                  </Form.Select>
                </Col>
              </Form.Group>
            </Col>

            <Col xs={12} sm={6} md={6}>
              <Form.Group as={Row} className="mb-3" controlId="users_email">
                <Form.Label column sm={3} md={4}>
                  Email
                </Form.Label>

                <Col sm={9} md={8}>
                  <Form.Control
                    type="email"
                    value={users_email}
                    onChange={(e) => setUsers_email(e.target.value)}
                    placeholder="Email..."
                    autoComplete="off"
                    required
                  />
                </Col>
              </Form.Group>
            </Col>
          </Row>

          <hr />

          <Row>
            <Col xs={12} sm={12} md={6}>
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

            <Col xs={12} sm={12} md={6}>
              <Form.Group as={Row} className="mb-3" controlId="users_last_name">
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

            <Col xs={12} sm={12} md={6}>
              <Form.Group as={Row} className="mb-3" controlId="users_nickname">
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

            <Col xs={12} sm={12} md={6}>
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
                    <option value={1}>Citizenship Card</option>
                    <option value={2}>Passport</option>
                  </Form.Select>
                </Col>
              </Form.Group>
            </Col>

            <Col xs={12} sm={12} md={6}>
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

          <div className="d-grid gap-2 d-md-flex justify-content-md-end">
            <Button type="submit" variant="warning">
              Save Changes
            </Button>
          </div>
        </Form>
      </div>
    </Container>
  );
}
