import React from "react";

export default class GalleryUploadForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            preview: null,
            uploadProgress: 0,
            isUploading: false
        }
    }

    componentDidMount() {
        this.generatePreview();
    }

    async generatePreview() {
        let rtn = [];
        let queue = $.map(this.props.uploadFiles, (value, index) => {
            return new Promise((resolve) => {
                let reader = new FileReader();
                reader.onloadend = (evt) => {
                    resolve({
                        name: value.name,
                        size: value.size,
                        dataUri: reader.result
                    });
                };
                reader.readAsDataURL(value)
            });
        });
        let preview = await Promise.all(queue);
        $.each(preview, (index, value) => {
            rtn.push(
                <div className="gallery-upload-form__img-wrapper col-3" key={index} >
                    <div className={"gallery-upload-form__img-outline"}>
                        <div className={"gallery-upload-form__img"} style={{backgroundImage: "url(" + value.dataUri + ")"}}>
                            <svg viewBox="0 0 1 1">
                            </svg>
                        </div>
                    </div>
                </div>
            )
        });
        this.setState({
            preview: rtn
        });
    }
    render() {
        return (
            <div className={"gallery-upload-form col-lx-6 col-lg-8 col-md-10 col-12 mx-auto"}>
                <div className={"gallery-upload-form__header row mb-5"}>
                    <div
                        className={"gallery-upload-form__close-btn btn-like text-secondary col-auto py-3 ml-3"}
                        onClick={() => {
                            this.props.onClose()
                        }}>
                        <i className="fas fa-times"/>
                    </div>
                    <div className={"col py-3 text-secondary"}>
                        Upload
                    </div>
                    {this.state.isUploading ? (
                        <div className="col-1 mr-3">
                            <div className={"gallery-upload-form__spinner h-100"}/>
                        </div>
                    ) : (
                        <div className={"col-auto py-3 mr-3 text-primary btn-like"} onClick={() => {
                            this.props.onSubmit({
                                name: $("[name='title']").val(),
                                uploadFiles: this.props.uploadFiles,
                                onProgress: (evt) => {
                                    // Upload is 80%, last 20% is getting request
                                    let progress = Math.round((evt.loaded / evt.total) * 100 * 0.8);
                                    this.setState({
                                        uploadProgress: progress,
                                        isUploading: true
                                    })
                                },
                                onFinish: () => {
                                    this.setState({
                                        uploadProgress: 100,
                                    })
                                }
                            })
                        }}>
                            Done
                        </div>)
                    }

                    <div className={"gallery-upload-form__progress-bar"} style={{width: this.state.uploadProgress + "%"}}/>
                </div>
                <div className={"px-5"}>
                    <div className="row">
                        <div className="col">
                            <form action={"#"}>
                                <div className={"form-group"}>
                                    <label>Name your photos:</label>
                                    <input name={"title"} type={"text"} className={"form-control form-control-sm"} placeholder={"Untitled"}/>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div className={"row px-3 my-3"}>
                        {this.state.preview}
                    </div>
                </div>
            </div>
        );
    }
}