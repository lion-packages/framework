import { Fragment, useContext, useEffect } from "react";
import { Outlet, useNavigate } from "react-router-dom";
import { Container, Nav } from "react-bootstrap";
import { ProfileContext } from "../../../context/account/ProfileContext";

export default function ProfileIndex() {
  const navigate = useNavigate();
  const { handleReadProfile } = useContext(ProfileContext);

  useEffect(() => {
    handleReadProfile();
  }, []);

  return (
    <Fragment>
      <Nav
        activeKey="general"
        onSelect={(selectedKey) => navigate(selectedKey)}
        className="bg-light text-center py-2 shadow-sm"
      >
        <Nav.Item>
          <Nav.Link eventKey="general" className="text-warning">
            General
          </Nav.Link>
        </Nav.Item>

        <Nav.Item>
          <Nav.Link eventKey="information" className="text-warning">
            Information
          </Nav.Link>
        </Nav.Item>

        <Nav.Item>
          <Nav.Link eventKey="change-password" className="text-warning">
            Change Password
          </Nav.Link>
        </Nav.Item>
      </Nav>

      <Container>
        <div className="my-3">
          <Outlet />
        </div>
      </Container>
    </Fragment>
  );
}
