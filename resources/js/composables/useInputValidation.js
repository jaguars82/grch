export function useInputValidation() {
  const onlyNumbersWithDot = (event) => {
    const key = event.key;
    const currentValue = event.target.value;

    // Allow: digits (0-9), a dot (.), Backspace, Delete, Arrows
    if (!/[0-9.]/.test(key) && key !== 'Backspace' && key !== 'Delete' && !key.startsWith('Arrow')) {
      event.preventDefault();
      return;
    }

    // Allow the only 'dot'
    if (key === '.' && currentValue.includes('.')) {
      event.preventDefault();
    }
  };

  return { onlyNumbersWithDot };
}