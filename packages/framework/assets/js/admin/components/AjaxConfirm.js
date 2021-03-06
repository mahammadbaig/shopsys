import Ajax from '../../common/utils/Ajax';
import Window from '../utils/Window';
import Register from '../../common/utils/Register';
import ConfirmDelete from './ConfirmDelete';

export default class AjaxConfirm {

    static bind () {
        const _this = this;
        $(this)
            .off('click.ajaxConfirm')
            .on('click.ajaxConfirm', function () {
                Ajax.ajax({
                    url: $(this).attr('href'),
                    context: this,
                    success: function (data) {
                        // eslint-disable-next-line no-new
                        new Window({
                            content: data
                        });
                        const onOpen = $(_this).data('ajax-confirm-on-open');
                        if (onOpen) {
                            // eslint-disable-next-line no-new
                            new ConfirmDelete(this);
                        }
                    }
                });

                return false;
            });
    }

    static init ($container) {
        $container.filterAllNodes('a.js-ajax-confirm').each(AjaxConfirm.bind);
    }
}

(new Register()).registerCallback(AjaxConfirm.init, 'AjaxConfirm.init');
