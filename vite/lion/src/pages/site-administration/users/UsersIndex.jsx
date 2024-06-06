/* eslint-disable no-unused-vars */
import { Container } from "react-bootstrap";
import { useState } from "react";
import UsersCreate from "./components/UsersCreate";
import UsersRead from "./components/UsersRead";
import Buttons from "./components/Buttons";

export default function UsersIndex() {
  const [show, setShow] = useState(false);

  return (
    <Container>
      <div className="my-3">
        <div className="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h4>Users</h4>

          <Buttons setShow={setShow} />
        </div>

        <UsersCreate show={show} setShow={setShow} />

        <UsersRead />
      </div>
    </Container>
  );
}
