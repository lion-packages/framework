/* eslint-disable react-hooks/exhaustive-deps */
import { Button, Col, Container, Form, Row } from "react-bootstrap";
import { useNavigate, useParams } from "react-router-dom";
import { useContext, useEffect, useState } from "react";
import useApiResponse from "../../../../hooks/useApiResponse";
import { AuthContext } from "../../../../context/AuthContext";
import { ResponseContext } from "../../../../context/ResponseContext";
import axiosApi from "../../../../Api";

export default function UsersUpdate() {
  const navigate = useNavigate();
  const { refreshToken } = useContext(AuthContext);
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

  const handleReadUsersById = async () => {
    const res = await axiosApi(refreshToken).get(`/api/users/${idusers}`);

    if (!res.data.status) {
      setIdroles(res.data.idroles);
      setIddocument_types(res.data.iddocument_types);
      setUsers_name(res.data.users_name);
      setUsers_last_name(res.data.users_last_name);
      setUsers_nickname(res.data.users_nickname);
      setUsers_citizen_identification(res.data.users_citizen_identification);
      setUsers_email(res.data.users_email);
    }

    if (res.response && 403 === res.response.data.code) {
      addToast([
        {
          status: res.response.data.status,
          title: "Users",
          message: res.response.data.message,
        },
      ]);
    }
  };

  const handleSubmit = async (event) => {
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

    const res = await axiosApi(refreshToken).put(`/api/users/${idusers}`, form);

    if (res.data) {
      addToast([
        {
          status: res.data.status,
          title: "Users Update",
          message: res.data.message,
        },
      ]);

      if (200 === res.data.code) {
        navigate(`/site-administration/users`);
      }
    }

    if (res.response) {
      if (500 === res.response.data.code) {
        addToast([
          ...getResponseFromRules("Update Users", res.response.data),
          {
            status: res.response.data.status,
            title: "Update Users",
            message: res.response.data.message,
          },
        ]);
      }
    }
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
