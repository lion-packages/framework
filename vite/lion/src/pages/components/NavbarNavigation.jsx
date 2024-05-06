import { Container, Image, Nav, NavDropdown, Navbar } from "react-bootstrap";
import { LinkContainer } from "react-router-bootstrap";
import logo from "../../assets/img/icon white.png";

export default function NavbarNavigation() {
  return (
    <Navbar expand="lg" className="bg-body-tertiary shadow-sm">
      <Container>
        <LinkContainer to={"/"}>
          <Navbar.Brand href="#">
            <Image src={logo} width={40} />

            <label role="button">
              Lion-
              <span style={{ color: "#ff8625" }}>Packages</span>
            </label>
          </Navbar.Brand>
        </LinkContainer>

        <Navbar.Toggle aria-controls="basic-navbar-nav" />

        <Navbar.Collapse id="basic-navbar-nav">
          <Nav className="ms-auto">
            <LinkContainer to={"/auth/login"}>
              <Nav.Link href="#">Login</Nav.Link>
            </LinkContainer>

            <LinkContainer to={"/auth/register"}>
              <Nav.Link href="#">Register</Nav.Link>
            </LinkContainer>

            {/* <NavDropdown title="Dropdown" id="basic-nav-dropdown">
              <NavDropdown.Item href="#action/3.1">Action</NavDropdown.Item>
              <NavDropdown.Item href="#action/3.2">
                Another action
              </NavDropdown.Item>
              <NavDropdown.Item href="#action/3.3">Something</NavDropdown.Item>
              <NavDropdown.Divider />
              <NavDropdown.Item href="#action/3.4">
                Separated link
              </NavDropdown.Item>
            </NavDropdown> */}
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
}
