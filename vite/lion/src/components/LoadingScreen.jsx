export default function LoadingScreen() {
  return (
    <div
      className="d-flex justify-content-center align-items-center"
      style={{ height: "100vh" }}
    >
      <div className="w-50">
        <div className="progress">
          <div
            className="progress-bar progress-bar-striped progress-bar-animated"
            role="progressbar"
            style={{ width: "100%" }}
          ></div>
        </div>
      </div>
    </div>
  );
}
