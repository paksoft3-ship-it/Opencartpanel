// NovaKur Base event helpers
export function on(eventName, selector, handler) {
  document.addEventListener(eventName, (event) => {
    const target = event.target.closest(selector);
    if (target) handler(event, target);
  });
}
