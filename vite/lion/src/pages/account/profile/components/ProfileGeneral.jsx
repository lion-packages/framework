import { Fragment } from "react";
import { Button, Col, Form, Row } from "react-bootstrap";
import { useProfile } from "../../../../context/account/ProfileProvider";

export default function ProfileGeneral() {
  const {
    handleUpdateProfile,
    users_name,
    users_last_name,
    users_nickname,
    iddocument_types,
    users_citizen_identification,
    setUsers_name,
    setUsers_last_name,
    setUsers_nickname,
    setIddocument_types,
    setUsers_citizen_identification,
  } = useProfile();

  return (
    <Fragment>
      <Form onSubmit={handleUpdateProfile}>
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

          <Col xs={12} sm={12} md={6} lg={6} xl={4}>
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

          <Col xs={12} sm={12} md={6} lg={6} xl={4}>
            <Form.Group as={Row} className="mb-3" controlId="iddocument_types">
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

        <Button type="submit" variant="warning" className="float-end">
          Save Changes
        </Button>
      </Form>
    </Fragment>
  );
}
