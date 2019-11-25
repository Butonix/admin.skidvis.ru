import ChangeSubmit from './inits/changeSubmit';
import SubmitAjax from './inits/submitAjax';
import Checkboxes from './inits/checkboxes';
import ChangePassword from './inits/changePassword';
import DeleteElements from './inits/deleteElements';
import ResetModals from './inits/resetModals';
import ChangeSubmitAjax from './inits/changeSubmitAjax';
import SwitchOrdering from './inits/switchOrdering';
import ImagesDownloadPreview from './inits/imagesDownloadPreview';
import MakeReadNotification from './inits/makeReadNotification';

export function initialize() {
    ChangeSubmit.init();
    SubmitAjax.init();
    Checkboxes.init();
    ChangePassword.init();
    DeleteElements.init();
    ResetModals.init();
    ChangeSubmitAjax.init();
    SwitchOrdering.init();
    ImagesDownloadPreview.init();
    MakeReadNotification.init();
}
