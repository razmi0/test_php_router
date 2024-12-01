export const counter = (e) => {
  const output = e.target.firstElementChild;
  const currentValue = parseInt(output.textContent || "0");
  output.textContent = currentValue + 1;
};
