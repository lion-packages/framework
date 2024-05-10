import { Fragment, useEffect } from "react";
import { Outlet, useNavigate } from "react-router-dom";
import { useProfile } from "../../../context/account/ProfileProvider";
import { Container, Nav } from "react-bootstrap";

export default function ProfileIndex() {
  const navigate = useNavigate();
  const { handleReadProfile } = useProfile();

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
      </Nav>

      <Container>
        <div className="my-3">
          <Outlet />
        </div>
      </Container>
    </Fragment>
  );
}
