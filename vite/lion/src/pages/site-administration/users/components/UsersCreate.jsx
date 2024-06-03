/* eslint-disable react/prop-types */
import { Button, Col, Form, Modal, Row } from "react-bootstrap";
import useApiResponse from "../../../../hooks/useApiResponse";
import { useContext, useState } from "react";
import sha256 from "crypto-js/sha256";
import axios from "axios";
import { AuthContext } from "../../../../context/AuthContext";
import { ResponseContext } from "../../../../context/ResponseContext";
import { UsersContext } from "../../../../context/site-administration/UsersContext";

export default function UsersCreate({ show, setShow }) {
  const { getJWT } = useContext(AuthContext);
  const { addToast } = useContext(ResponseContext);
  const { getResponseFromRules } = useApiResponse();
  const { handleReadUsers } = useContext(UsersContext);

  const [idroles, setIdroles] = useState("");
  const [users_name, setUsers_name] = useState("");
  const [users_last_name, setUsers_last_name] = useState("");
  const [users_nickname, setUsers_nickname] = useState("");
  const [iddocument_types, setIddocument_types] = useState("");
  const [users_citizen_identification, setUsers_citizen_identification] =
    useState("");
  const [users_email, setUsers_email] = useState("");
  const [users_password, setUsers_password] = useState("");

  const handleSubmit = (event) => {
    event.preventDefault();

    const form = {
      idroles: parseInt(idroles),
      users_name: users_name,
      users_last_name: users_last_name,
      users_nickname: users_nickname,
      iddocument_types: parseInt(iddocument_types),
      users_citizen_identification: users_citizen_identification,
      users_email: users_email,
      users_password: sha256(users_password).toString(),
    };

    axios
      .post(`${import.meta.env.VITE_SERVER_URL_AUD}/api/users`, form, {
        headers: {
          Authorization: `Bearer ${getJWT()}`,
        },
      })
      .then(({ data }) => {
        addToast([
          {
            status: data.status,
            title: "Create Users",
            message: data.message,
          },
        ]);

        if (200 === data.code) {
          setIdroles("");

          setUsers_name("");

          setUsers_last_name("");

          setUsers_nickname("");

          setIddocument_types("");

          setUsers_citizen_identification("");

          setUsers_email("");

          setUsers_password("");

          setShow(false);

          handleReadUsers();
        }
      })
      .catch(({ response }) => {
        if ([400, 403].includes(response.data.code)) {
          addToast([
            {
              status: response.data.status,
              title: "Create Users",
              message: response.data.message,
            },
          ]);
        }

        if (500 === response.data.code) {
          if (response.data.data["rules-error"]) {
            addToast([...getResponseFromRules("Create Users", response.data)]);
          } else {
            addToast([
              {
                status: response.data.status,
                title: "Create Users",
                message: response.data.message,
              },
            ]);
          }
        }
      });
  };

  return (
    <Modal show={show} size="lg" onHide={() => setShow(false)}>
      <Modal.Header closeButton>
        <Modal.Title>Create Users</Modal.Title>
      </Modal.Header>

      <Form onSubmit={handleSubmit}>
        <Modal.Body>
          <Form.Group as={Row} className="mb-3" controlId="idroles">
            <Form.Label column sm={3}>
              Rol
            </Form.Label>

            <Col sm={9}>
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
                autoComplete="off"
              />
            </Col>
          </Form.Group>

          <hr />

          <Form.Group as={Row} className="mb-3" controlId="users_name">
            <Form.Label column sm={3}>
              Name
            </Form.Label>

            <Col sm={9}>
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

          <Form.Group as={Row} className="mb-3" controlId="users_last_name">
            <Form.Label column sm={3}>
              Last name
            </Form.Label>

            <Col sm={9}>
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

          <Form.Group as={Row} className="mb-3" controlId="users_nickname">
            <Form.Label column sm={3}>
              Nickname
            </Form.Label>

            <Col sm={9}>
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

          <Form.Group as={Row} className="mb-3" controlId="iddocument_types">
            <Form.Label column sm={3}>
              ID Type
            </Form.Label>

            <Col sm={9}>
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

          <Form.Group
            as={Row}
            className="mb-3"
            controlId="users_citizen_identification"
          >
            <Form.Label column sm={3}>
              ID
            </Form.Label>

            <Col sm={9}>
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
        </Modal.Body>

        <Modal.Footer>
          <Button variant="secondary" onClick={() => setShow(false)}>
            Close
          </Button>

          <Button type="submit" variant="success">
            Save Changes
          </Button>
        </Modal.Footer>
      </Form>
    </Modal>
  );
}
