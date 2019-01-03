import React from 'react';
import axios from 'axios';

export default class GalleryUpload extends React.Component {
    constructor(props) {
        super(props);
    }

    onBtnClick() {
        $("[name='gallery[file-upload]']").trigger("click");
    }

    componentDidMount() {
        $(document).on("change", "[name='gallery[file-upload]']", async () => {
            let files = $("[name='gallery[file-upload]']").prop("files");
            console.log(files);
            let formData = new FormData();
            $.each(files, (index, file) => {
                formData.append("image_" + index, file);
            });
            for(let pair of formData.entries()) {
                console.log(pair[0]+ ', '+ pair[1]);
            }
            let rsp = await axios.post(this.props.ajaxUrl, formData);
            console.log(rsp);
        })
    }

    render() {
        return (
            <div>
                <button type={"button"} className={"btn btn-primary btn-sq"} onClick={() => {
                    this.onBtnClick();
                }}>
                    <i className="fas fa-camera"/>
                </button>
                <input type="file" className="d-none" accept="image/*" name="gallery[file-upload]" multiple/>
            </div>
        )
    }
}