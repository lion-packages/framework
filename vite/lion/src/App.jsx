import axios from "axios";
import { useEffect, useState } from "react";

function App() {
  const [response, setResponse] = useState({
    message: "",
    status: "",
    code: 0,
  });

  useEffect(() => {
    axios.get("http://127.0.0.1:8000").then((res) => setResponse(res.data));
  }, []);

  return (
    <div className="bg-dark vh-100">
      <div className="container-fluid pt-5">
        <div className="px-3 py-5 mx-auto col-6 text-white border rounded">
          <h1 className="text-center">Lion-Framework</h1>

          <hr />

          <p>
            <span className="fw-6">
              <strong>Status:</strong>
            </span>{" "}
            <span className={"badge text-bg-" + response.status}>
              {response.status}
            </span>
          </p>

          <p>
            <span className="fw-6">
              <strong>HTTP Code:</strong>
            </span>{" "}
            {response.code}
          </p>

          <p>
            <span className="fw-6">
              <strong>Message:</strong>
            </span>{" "}
            {response.message}
          </p>
        </div>
      </div>
    </div>
  );
}

export default App;
