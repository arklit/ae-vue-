import './validation/validation';
import './tree';
import './mobile-commandbar';
import './upload-sort';
import CropperRocont from "./controllers/cropper.js";
import TinyMceController from "./controllers/tinymce";
import UploaderRocont from "./controllers/uploader.js";

application.register("cropperrocont", CropperRocont);
application.register("tinymce", TinyMceController);
application.register("uploaderrocont", UploaderRocont);
