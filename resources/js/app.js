import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { registerMotion, registerBottomNav } from './lib/motion';
import { registerToast } from './lib/toast';
import { registerPalette } from './lib/palette';
import { registerModal } from './lib/modal';

window.Alpine = Alpine;

Alpine.plugin(focus);
registerMotion(Alpine);
registerBottomNav(Alpine);
registerToast(Alpine);
registerPalette(Alpine);
registerModal();

Alpine.start();
