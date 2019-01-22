import React from 'react';
import axios from 'axios';

export default class GalleryUpload extends React.Component {
    constructor(props) {
        super(props);
    }

    onBtnClick() {
        $("#js-upload").val("").trigger("click");
    }

    componentDidMount() {
        $(document).on("change", "#js-upload", async () => {
            let files = $("#js-upload").prop("files");
            if (files.length > 0) {
                this.props.onTrigger(files);
            }
        })
    }

    render() {
        return (
            <div>
                <button type={"button"} className={"btn btn-primary btn-rd shadow-lg"} onClick={() => {
                    this.onBtnClick();
                }}>
                    <i className="fas fa-camera"/>
                </button>
                <input type="file" id={"js-upload"} className="d-none" accept="image/png,image/jpeg,image/bmp" name="upload[]" multiple/>

            </div>
        )
    }
}