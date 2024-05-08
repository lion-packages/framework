import { Container, Image, Nav, NavDropdown, Navbar } from "react-bootstrap";
import { LinkContainer } from "react-router-bootstrap";
import logo from "../../assets/img/icon-white.png";
import { useAuth } from "../../context/AuthProvider";
import { Fragment } from "react";
import { useResponse } from "../../context/ResponseProvider";
import { useNavigate } from "react-router-dom";

export default function Header() {
  const navigate = useNavigate();
  const { jwt, logout } = useAuth();
  const { addToast } = useResponse();

  return (
    <Navbar expand="lg" className="bg-body-tertiary shadow-sm">
      <Container>
        <LinkContainer to={jwt ? "/dashboard" : "/auth/login"}>
          <Navbar.Brand href={"#"}>
            <Image src={logo} width={35} />

            <label role="button">
              Lion-
              <span style={{ color: "#ff8625" }}>Packages</span>
            </label>
          </Navbar.Brand>
        </LinkContainer>

        <Navbar.Toggle aria-controls="basic-navbar-nav" />

        <Navbar.Collapse id="basic-navbar-nav">
          <Nav className="ms-auto">
            {!jwt && (
              <Fragment>
                <LinkContainer to={"/auth/login"}>
                  <Nav.Link href="#">Login</Nav.Link>
                </LinkContainer>

                <LinkContainer to={"/auth/register"}>
                  <Nav.Link href="#">Register</Nav.Link>
                </LinkContainer>
              </Fragment>
            )}

            {jwt && (
              <Fragment>
                <NavDropdown title="Options" id="basic-nav-dropdown">
                  <NavDropdown.Item
                    href="#"
                    onClick={() => {
                      logout();

                      addToast([
                        {
                          status: "info",
                          title: "Sign off",
                          message: "You have been signed off",
                        },
                      ]);

                      navigate("/auth/login");
                    }}
                  >
                    Logout
                  </NavDropdown.Item>
                </NavDropdown>
              </Fragment>
            )}
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
}
