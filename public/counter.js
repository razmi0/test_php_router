export const counter = (element) => {
  const currentValue = parseInt(element.textContent);
  element.textContent = currentValue + 1;
};
