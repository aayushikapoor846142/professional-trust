const CustomEditor = (() => {
  let editor, textarea;

  function createPopup() {
    const popup = document.createElement('div');
    popup.style.position = 'fixed';
    popup.style.top = '50%';
    popup.style.left = '50%';
    popup.style.transform = 'translate(-50%, -50%)';
    popup.style.background = '#fff';
    popup.style.border = '1px solid #ccc';
    popup.style.padding = '10px';
    popup.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
    popup.style.zIndex = '1000';
    popup.style.display = 'none';

    const label = document.createElement('label');
    label.textContent = 'Enter value: ';

    const textInput = document.createElement('input');
    textInput.type = 'text';
    textInput.style.margin = '10px 0';
    textInput.style.width = '250px';

    const colorInput = document.createElement('input');
    colorInput.type = 'color';
    colorInput.style.margin = '10px 0';
    colorInput.style.width = '100px';
    colorInput.style.display = 'none';

    const confirm = document.createElement('button');
    confirm.textContent = 'Apply';

    const cancel = document.createElement('button');
    cancel.textContent = 'Cancel';
    cancel.style.marginLeft = '10px';

    const container = document.createElement('div');
    container.append(label, textInput, colorInput, confirm, cancel);
    popup.append(container);
    document.body.appendChild(popup);

    return {
      show: (cmd, placeholder = '', type = 'text') => {
        popup.style.display = 'block';
        textInput.style.display = type === 'color' ? 'none' : 'inline-block';
        colorInput.style.display = type === 'color' ? 'inline-block' : 'none';

        if (type === 'color') {
          colorInput.value = '#000000';
          colorInput.focus();
        } else {
          textInput.value = '';
          textInput.placeholder = placeholder;
          textInput.focus();
        }

        confirm.onclick = () => {
          popup.style.display = 'none';
          const value = type === 'color' ? colorInput.value : textInput.value;
          editor.focus();
          document.execCommand(cmd, false, value);
        };

        cancel.onclick = () => {
          popup.style.display = 'none';
        };
      }
    };
  }

  const popupManager = createPopup();

  function init(selector) {
    textarea = document.querySelector(selector);
    if (!textarea) {
      console.error('Textarea not found:', selector);
      return;
    }

    const container = document.createElement('div');
    container.style.border = '1px solid #ccc';
    container.style.padding = '5px';

    const toolbar = document.createElement('div');
    toolbar.style.marginBottom = '5px';

    editor = document.createElement('div');
    editor.contentEditable = true;
    editor.innerHTML = textarea.value;
    editor.style.minHeight = '150px';
    editor.style.border = '1px solid #eee';
    editor.style.padding = '8px';

    const buttons = [
      { cmd: 'bold', label: '<b>B</b>' },
      { cmd: 'italic', label: '<i>I</i>' },
      { cmd: 'underline', label: '<u>U</u>' },
      { cmd: 'strikeThrough', label: '<s>S</s>' },
      { cmd: 'formatBlock', label: 'H1', value: 'h1' },
      { cmd: 'formatBlock', label: 'H2', value: 'h2' },
      { cmd: 'formatBlock', label: 'P', value: 'p' },
      { cmd: 'formatBlock', label: 'Quote', value: 'blockquote' },
      { cmd: 'insertOrderedList', label: '1.' },
      { cmd: 'insertUnorderedList', label: '•' },
      { cmd: 'justifyLeft', label: 'Left' },
      { cmd: 'justifyCenter', label: 'Center' },
      { cmd: 'justifyRight', label: 'Right' },
      { cmd: 'foreColor', label: 'Text Color', custom: true, type: 'color' },
      { cmd: 'hiliteColor', label: 'Background Color', custom: true, type: 'color' },
      { cmd: 'createLink', label: 'Link', custom: true, placeholder: 'e.g. https://example.com' },
      { cmd: 'unlink', label: 'Unlink' },
      { cmd: 'insertImage', label: 'Image', custom: true, placeholder: 'Image URL' },
      { cmd: 'insertHorizontalRule', label: 'HR' },
      { cmd: 'undo', label: 'Undo' },
      { cmd: 'redo', label: 'Redo' }
    ];

    buttons.forEach(({ cmd, label, value, custom, placeholder, type }) => {
      const btn = document.createElement('button');
      btn.innerHTML = label;
      btn.type = 'button';
      btn.className = 'cds-ce-btn';
      btn.style.marginRight = '4px';
      btn.style.marginBottom = '4px';
      btn.onclick = () => {
        editor.focus();
        if (custom) {
          popupManager.show(cmd, placeholder || '', type || 'text');
        } else {
          document.execCommand(cmd, false, value || null);
        }
      };
      toolbar.appendChild(btn);
    });

    editor.addEventListener('input', () => {
      textarea.value = editor.innerHTML;
    });

    textarea.style.display = 'none';
    container.append(toolbar, editor);
    textarea.parentNode.insertBefore(container, textarea);
  }

  return { init };
})();
