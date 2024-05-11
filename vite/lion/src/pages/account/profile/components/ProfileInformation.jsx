import { Col, Form, Row } from "react-bootstrap";
import { useProfile } from "../../../../context/account/ProfileProvider";

export default function ProfileInformation() {
  const { idroles, setIdroles, setUsers_email, users_email } = useProfile();

  return (
    <Row>
      <Col xs={12} sm={6} md={6} lg={6} xl={4}>
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
              <option value={1}>Administrator</option>
              <option value={2}>Manager</option>
              <option value={3}>Customer</option>
            </Form.Select>
          </Col>
        </Form.Group>
      </Col>

      <Col xs={12} sm={6} md={6} lg={6} xl={4}>
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
              autoComplete="off"
            />
          </Col>
        </Form.Group>
      </Col>
    </Row>
  );
}
