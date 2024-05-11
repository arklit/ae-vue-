import Cropper from 'cropperjs';
import tingle from 'tingle.js'

export default class CropperRocont extends window.Controller {

    static selected = null
    static targets = [
        "source",
        "upload"
    ];

    /**
     *
     */
    connect() {
        let image = this.data.get('url') ? this.data.get('url') : this.data.get(`value`);

        const preview = this.element.querySelector('.cropper-preview');
        const remove = this.element.querySelector('.cropper-remove');

        if (image) {
            preview.src = image;
        } else {
            preview.classList.add('none');
            remove.classList.add('none');
        }
    }

    /**
     *
     * @returns {Modal}
     */
    getModal() {
        const { width, height } = this.data.get;

        const html = `
            <div class="modal-to-clone" role="dialog">
                <div class="modal-dialog modal-fullscreen-md-down modal-lg">
                    <div class="modal-content-wrapper">
                        <div class="modal-content">
                            <div class="position-relative">
                                <img class="upload-panel">
                            </div>
                        </div>
                    </div>
                </div>
                </div>`;

        this.modal = new tingle.modal({
            footer: true,
            stickyFooter: false,
            closeMethods: ['overlay'],
            closeLabel: "Close",
            cssClass: ['custom-class-1', 'custom-class-2'],
            onOpen: function () {
                this.setContent(html);
                let cropPanel = document.querySelector('.upload-panel');

                cropPanel.width = width;
                cropPanel.height = height;

                window.cropper = new Cropper(cropPanel, {
                    viewMode: 2,
                    aspectRatio: width / height,
                    minContainerHeight: 500,
                });

            },
            onClose: function () {
                this.destroy();
            },
            beforeClose: function () {
                return true;
            }
        });

        this.modal.addFooterBtn('Закрыть', 'btn btn-link', () => {
            this.modal.close()
        });

        this.modal.addFooterBtn('Обрезать', 'btn btn-default', () => {
            this.crop(this.modal)
        });

        this.clearEvent()

        return this.modal;
    }

    /**
     * Event for uploading image
     *
     * @param event
     */
    async upload(event) {
        const maxFileSize = this.data.get('max-file-size');
        if (maxFileSize !== null && event.target.files[0].size / 1024 / 1024 > maxFileSize) {
            this.alert('Validation error', `The download file is too large. Max size: ${maxFileSize} MB`);
            event.target.value = null;
            return;
        }

        this.selected = event.target.closest('.cropper-parent')
        this.getModal().open();

        let reader = new FileReader();
        reader.readAsDataURL(event.target.files[0]);
        await new Promise(resolve => {
            reader.onloadend = () => {
                window.cropper.replace(reader.result);
                resolve();
            };
        });

        document.querySelector('.cropper-path').dispatchEvent(new Event("change"));
    }

    /**
     * Action on click button "Crop"
     */
    crop(modal) {
        window.cropper.getCroppedCanvas().toBlob((blob) => {
            const formData = new FormData();
            formData.append('file', blob);
            formData.append('storage', this.data.get('storage'));
            formData.append('group', this.data.get('groups'));
            formData.append('path', this.data.get('path'));
            formData.append('acceptedFiles', this.data.get('accepted-files'));
            formData.append('width', this.data.get('width'));
            formData.append('height', this.data.get('height'));

            window.axios.post(this.prefix('/systems/files'), formData)
                .then((response) => {
                    let image = response.data.url;
                    let targetValue = this.data.get('target');

                    this.selected.querySelector('.cropper-preview').src = image;
                    this.selected.querySelector('.cropper-preview').classList.remove('none');
                    this.selected.querySelector('.cropper-remove').classList.remove('none');
                    this.selected.querySelector('.cropper-path').value = response.data[targetValue];

                    // add event for listener
                    this.selected.querySelector('.cropper-path').dispatchEvent(new Event("change"));

                    modal.close();
                    this.selected = null
                })
                .catch((error) => {
                    this.alert('Validation error', 'File upload error');
                    console.warn(error);
                });
        });

    }

    /**
     *
     */
    clear(event) {
        this.selected = event.target.closest('.cropper-parent')
        if (this.selected) {
            this.selected.querySelector('.cropper-path').value = '';
            this.selected.querySelector('.cropper-preview').src = '';
            this.selected.querySelector('.cropper-preview').classList.add('none');
            this.selected.querySelector('.cropper-remove').classList.add('none');
        }

        this.selected = null
    }

    clearEvent() {
        let modal = document.querySelector('.tingle-modal');
        let btn = modal.querySelector('.btn.btn-link');

        btn.addEventListener('click', (event) => {
            this.clear(event)
        });

        window.addEventListener('click', (event) => {
            if (!event.target.closest('.tingle-modal')) {
                this.clear(event)
            }
        });
    }

    /**
     * Action on click buttons
     */
    moveleft() {
        this.cropper.move(-10, 0);
    }

    moveright() {
        this.cropper.move(10, 0);
    }

    moveup() {
        this.cropper.move(0, -10);
    }

    movedown() {
        this.cropper.move(0, 10);
    }

    zoomin() {
        this.cropper.zoom(0.1);
    }

    zoomout() {
        this.cropper.zoom(-0.1);
    }

    rotateleft() {
        this.cropper.rotate(-5);
    }

    rotateright() {
        this.cropper.rotate(5);
    }

    scalex() {
        const dataScaleX = this.element.querySelector('.cropper-dataScaleX');
        this.cropper.scaleX(-dataScaleX.value);
    }

    scaley() {
        const dataScaleY = this.element.querySelector('.cropper-dataScaleY');
        this.cropper.scaleY(-dataScaleY.value)
    }

    aspectratiowh() {
        this.cropper.setAspectRatio(this.data.get('width') / this.data.get('height'));
    }

    aspectratiofree() {
        this.cropper.setAspectRatio(NaN);
    }
}
