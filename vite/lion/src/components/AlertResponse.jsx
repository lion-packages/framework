import { Toast, ToastContainer } from "react-bootstrap";
import logo from "../assets/img/icon-white.png";
import { useContext } from "react";
import { ResponseContext } from "../context/ResponseContext";

export default function AlertResponse() {
  const { toasts, removeToast } = useContext(ResponseContext);

  const getColor = (status) => {
    const colors = {
      success: "bg-success text-white",
      error: "bg-danger text-white",
      "database-error": "bg-danger text-white",
      "files-error": "bg-danger text-white",
      "session-error": "bg-danger text-white",
      "route-error": "bg-danger text-white",
      "rule-error": "bg-danger text-white",
      "mail-error": "bg-danger text-white",
      warning: "bg-warning text-dark",
      info: "bg-info text-white",
    };

    return colors[status] ?? colors.info;
  };

  return (
    <div style={{ position: "fixed", bottom: 20, right: 20, zIndex: 2000 }}>
      <ToastContainer className="position-static">
        {toasts.map((toast, index) => (
          <Toast
            key={`${index}-${toast.id}`}
            onClose={() => removeToast(toast.id)}
            autohide
            delay={3500 + (index + 1) * 1000}
          >
            <Toast.Header closeButton={true} className={getColor(toast.status)}>
              <img
                src={logo}
                className={"rounded me-2"}
                alt={"lion-packages"}
                width={20}
              />

              <strong className="me-auto">{toast.title}</strong>

              {/* <small className="text-light">2 seconds ago</small> */}
            </Toast.Header>

            <Toast.Body className="bg-white">{toast.message}</Toast.Body>
          </Toast>
        ))}
      </ToastContainer>
    </div>
  );
}
