import { Fragment, useState } from "react";
import { Button } from "react-bootstrap";
import ChangePasswordModal from "./ChangePasswordModal";

export default function ProfileSecurity() {
  const [show, setShow] = useState(false);

  return (
    <Fragment>
      <div className="p-4 border rounded mt-3">
        <Fragment>
          <h3>Password and Authenticator</h3>

          <Button type="button" variant="warning" onClick={() => setShow(true)}>
            Change Password
          </Button>

          <ChangePasswordModal show={show} setShow={setShow} />
        </Fragment>

        <hr />

        <Fragment>
          <h5>AUTHENTICATOR APP</h5>

          <p>
            Protect your Lion-Packages account with an extra layer of security.
            Once configured, you will be prompted to enter your password and
            complete an additional sign-in step.
          </p>

          <Button>Enable Authenticator App</Button>
        </Fragment>
      </div>
    </Fragment>
  );
}
