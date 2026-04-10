RedactorX.add('plugin', 'fontfamily', {
    translations: {
        'en': {
            "fontfamily": {
                "title": "Font",
                "remove":  "Remove font family"
            }
        }
    },
    init() {
        this.fonts = (this.opts.fontfamily) ? this.opts.fontfamily : ['Arial', 'Helvetica', 'Georgia', 'Times New Roman', 'Monospace'];
    },
    start() {
        let button = {
            title: '## fontfamily.title ##',
            icon: '<svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.05618 2.37524C7.17767 2.05129 7.48736 1.83667 7.83334 1.83667H9.16667C9.49643 1.83667 9.79491 2.03188 9.92708 2.33399L14.3762 12.5033H14.5C14.9584 12.5033 15.33 12.8749 15.33 13.3333C15.33 13.7917 14.9584 14.1633 14.5 14.1633H13.8524C13.8399 14.1636 13.8275 14.1636 13.8152 14.1633H11.1855C11.1732 14.1636 11.1609 14.1636 11.1486 14.1633H9.83334C9.37494 14.1633 9.00334 13.7917 9.00334 13.3333C9.00334 12.8749 9.37494 12.5033 9.83334 12.5033H9.91398L9.20556 10.83H5.65853L5.03103 12.5033H5.16667C5.62507 12.5033 5.99667 12.8749 5.99667 13.3333C5.99667 13.7917 5.62507 14.1633 5.16667 14.1633H3.85246C3.83966 14.1636 3.82682 14.1636 3.81397 14.1633H3.16667C2.70827 14.1633 2.33667 13.7917 2.33667 13.3333C2.33667 12.8749 2.70827 12.5033 3.16667 12.5033H3.25815L7.05618 2.37524ZM6.28103 9.17H8.50278L7.32462 6.38709L6.28103 9.17ZM8.17134 4.12916L11.7166 12.5033H12.5643L8.62384 3.49667H8.40853L8.17134 4.12916Z"/></svg>',
            blocks: {
                all: 'editable',
                except: ['code']
            },
            command: 'fontfamily.popup'
        };

        this.app.toolbar.add('fontfamily', button);
    },
    popup(e, button) {
        let buttons = {};
        for (let i = 0; i < this.fonts.length; i++) {
            let font = this.fonts[i];
            let obj = {
                title: font.replace(/'/g, ''),
                command: 'inline.set',
                params: {
                    tag: 'span',
                    style: {
                        'font-family': font
                    }
                }
            };

            buttons[i] = obj;
        }

        // remove font family
        buttons['remove'] = {
            title: '## fontfamily.remove ##',
            divider: 'top',
            command: 'fontfamily.remove'
        };

        this.app.popup.create('fontfamily', {
            //width: '300px',
            items: buttons
        });
        this.app.popup.open({ button: button });
    },
    remove() {
        this.app.popup.close();
        this.app.inline.remove({ style: 'font-family' });
    }
});