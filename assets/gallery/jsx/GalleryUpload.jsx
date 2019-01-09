import React from 'react';
import axios from 'axios';

export default class GalleryUpload extends React.Component {
    constructor(props) {
        super(props);
    }

    onBtnClick() {
        $("[name='gallery[file-upload]']").val("").trigger("click");
    }

    componentDidMount() {
        $(document).on("change", "[name='gallery[file-upload]']", async () => {
            let files = $("[name='gallery[file-upload]']").prop("files");
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
                <input type="file" className="d-none" accept="image/*" name="gallery[file-upload]" multiple/>
            </div>
        )
    }
}