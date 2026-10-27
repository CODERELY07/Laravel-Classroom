// Entry point. Each initializer does nothing on pages without its markup.
import { setupAjax, showFlashes } from './core.js';
import { initForms } from './forms.js';
import { initAvatarPicker, initConfirm, initCopyButtons, initModalFocus, initPasswordToggles, initSidebar, initTabs } from './ui.js';
import { initClassGrid } from './classes.js';
import { initGrading, initPeoplePicker, initQuestionBuilder, initUnenroll } from './classwork.js';
import { initChat } from './chat.js';

setupAjax();
initForms();
initSidebar();
initTabs();
initCopyButtons();
initPasswordToggles();
initModalFocus();
initConfirm();
initAvatarPicker();
initClassGrid();
initQuestionBuilder();
initPeoplePicker();
initUnenroll();
initGrading();
initChat();
showFlashes();
