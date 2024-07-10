import { Fragment, useContext, useState } from "react";
import { Button } from "react-bootstrap";
import ChangePasswordModal from "./ChangePasswordModal";
import Enable2FAModal from "./Enable2FAModal";
import ConfirmPasswordModal from "./ConfirmPasswordModal";
import { AuthContext } from "../../../../context/AuthContext";

export default function ProfileSecurity() {
  const { auth_2fa } = useContext(AuthContext);

  const [show, setShow] = useState(false);
  const [show2FA, setShow2FA] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

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

          {!auth_2fa && (
            <Button
              type="button"
              variant="info"
              size="sm"
              onClick={() => setShowConfirmPassword(true)}
            >
              Enabled Authenticator App
            </Button>
          )}

          {auth_2fa && (
            <Button type="button" variant="danger" size="sm">
              Disabled Authenticator App
            </Button>
          )}

          <ConfirmPasswordModal
            show={showConfirmPassword}
            setShow={setShowConfirmPassword}
            setShow2FA={setShow2FA}
          />

          <Enable2FAModal show={show2FA} setShow={setShow2FA} />
        </Fragment>
      </div>
    </Fragment>
  );
}
