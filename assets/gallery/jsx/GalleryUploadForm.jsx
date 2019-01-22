import React from "react";
import GalleryNav from "./GalleryNav";
import BtnLikeText from "./Component/BtnLikeText"
import loadImage from "blueimp-load-image";
import axios from "axios";
export default class GalleryUploadForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            preview: null,
            progress: 0,
            isUploading: false,
        }
    }

    componentDidMount() {
        this.generatePreview();
    }

    async generatePreview() {
        let queue = [];
        for (let i = 0; i < this.props.uploadFiles.length; i++) {
            queue.push(new Promise((resolve, reject) => {
                let file = this.props.uploadFiles[i];
                loadImage(file, (canvas) => {
                    let base64 = canvas.toDataURL('image/jpeg');
                    resolve(
                        <div className="gallery-upload-form__img-wrapper col-3" key={i} >
                            <div className={"gallery-upload-form__img-outline"}>
                                <div className={"gallery-upload-form__img"} style={{backgroundImage: "url(" + base64 + ")"}}>
                                    <svg viewBox="0 0 1 1">
                                    </svg>
                                </div>
                            </div>
                        </div>
                    );
                }, {
                    canvas: true,
                    orientation: true,
                    cover: true,
                    maxWidth: 300,
                    maxHeight: 300
                })
            }))
        }
        let preview = await Promise.all(queue);

        this.setState({
            preview: preview,
        });
    }
    async submit() {
        let data = new FormData();
        data.append("name", $("[name='title']").val());
        $.each(this.props.uploadFiles, (index, value) => {
            data.append("img_" + index, value);
        });
        // TODO: Need to limit file size and count
        let response = await axios.request({
            url: this.props.ajaxUrl,
            method: "post",
            data: data,
            onUploadProgress: (evt) => {
                let progress = Math.floor(evt.loaded * 100 / evt.total);
                this.setState({
                    progress: progress
                });
            }
        });
        if (200 === response.status) {
            this.props.modalClose();
        } else {
            console.error(response);
        }
    }
    render() {
        return (
            <div className={"row"}>
                <div className={"col-12 col-sm-9 col-md-7 col-lg-5 col-xl-4 mx-auto"}>
                    <div className={"gallery-upload-form"}>
                        <GalleryNav
                            onCloseBtnClick={this.props.modalClose}
                            header={"Upload"}
                            progress={this.state.progress}
                            actionBar={(
                                <div>
                                    <BtnLikeText
                                        content={"Done"}
                                        onClick={() => {
                                            return this.submit()
                                        }}
                                    />
                                </div>
                            )}
                        />
                        <div className={"gallery-upload-form__body"}>
                            <div className="row">
                                <div className="col">
                                    <div className={"form-group"}>
                                        <label class={"text-secondary"}>Enter a title for your collection:</label>
                                        <input name={"title"} type={"text"} className={"form-control form-control"} placeholder={"Untitled"} multiple={true}/>
                                    </div>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col text-secondary">
                                    <small>Maximum of 12 Photos:</small>
                                </div>
                            </div>
                            <div className={"row mb-3"} style={{
                                marginLeft: "-4px",
                                marginRight: "-4px"
                            }}>
                                {this.state.preview}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        );
    }
}