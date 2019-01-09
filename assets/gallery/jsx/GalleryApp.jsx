import React from 'react'
import axios from 'axios';
import GalleryItem from './GalleryItem';
import "./GalleryUpload";
import GalleryUpload from "./GalleryUpload";
import GalleryModal from  "./GalleryModal";
import GalleryViewer from "./GalleryViewer";
import GalleryUploadForm from "./GalleryUploadForm";

export default class GalleryApp extends React.Component {
    constructor(props) {
        super(props);
        this.storege = {};
        this.state = {
            uploadFiles: [],
            items: [],
            modal: {
                isVisible: false,
                isLoaded: true,
                content: null
            }
        };
    }

    modalClose() {
        history.replaceState(null, null, "/gallery");
        this.setState({
            modal: {
                isVisible: false,
                content: null
            }
        });
    }

    triggerUpload(files) {
        if (files.length >= 0) {
            this.setState({
                modal: {
                    isVisible: true,
                    content: (
                        <GalleryUploadForm
                            uploadFiles={files}
                            onClose={() => {
                                this.modalClose()
                            }}
                            onSubmit={(payload) => {
                                this.submitUpload(payload);
                            }}
                        />
                    )
                }
            })
        }
    }

    async submitUpload(payload) {
        let data = new FormData();
        data.append("name", payload.name);
        $.each(payload.uploadFiles, (index, value) => {
            data.append("img_" + index, value);
        });
        let config = {
            onUploadProgress: payload.onProgress
        };
        let response = await axios.post(this.props.ajaxUrl, data, config);
        if (200 === response.status) {
            payload.onFinish();
            window.setTimeout(() => {
                this.setState({
                    modal: {
                        isVisible: false,
                        content: (
                            null
                        )
                    }
                });
            }, 2000);
            this.populateGallery();
        } else {
            console.error(response);
        }
    }

    async deleteItem(payload) {
        let id = payload.id;
        let response = await axios.delete(this.props.ajaxUrl + "/" + id);
        if (200 === response.status) {
            this.populateGallery();
        } else {
            console.error(response);
        }
    }

    async likeItem(payload) {
        let id = payload.id;
        let response = await axios.post(this.props.ajaxUrl + "/" + id + "/likes");
        if (200 === response.status) {
        } else {
            console.error(response);
        }
    }

    async unlikeItem(payload) {
        let id = payload.id;
        let response = await axios.delete(this.props.ajaxUrl + "/" + id + "/likes");
        if (200 === response.status) {
        } else {
            console.error(response);
        }
    }

    expandItem(id) {
        let item = this.storege[id];
        if (item) {
            history.pushState({
                id: id
            }, null, "/gallery/" + id);
            this.setState({
                modal: {
                    isVisible: true,
                    content: (
                        <GalleryViewer
                            id={item.id}
                            header={item.header}
                            content={item.content}
                            asset={item.asset}
                            onClose={() => {
                                this.modalClose()
                            }}
                        />
                    )
                }
            })
        }
    };

    async populateGallery() {
        return axios.get(this.props.ajaxUrl).then((res) => {
            if (200 === res.status) {
                let items = $.map(res.data, (v, k) => {
                    this.storege[v.id] = v;
                    return (
                        <div className={"gallery-item-wrapper"} key={v.id}>
                            <GalleryItem
                                id={v.id}
                                header={v.header}
                                content={v.content}
                                thumbnail={v.thumbnail}
                                isLiked={v.isLiked}
                                isLoggedIn={this.props.isLoggedIn}
                                signInLink={this.props.signInLink}
                                onExpand={(payload) => {
                                    this.expandItem(payload)
                                }}
                                onDelete={(payload) => {
                                    this.deleteItem(payload)
                                }}
                                onLike={(payload) => {
                                    this.likeItem(payload)
                                }}
                                onUnlike={(payload) => {
                                    this.unlikeItem(payload)
                                }}
                            />
                        </div>
                    )});
                this.setState({
                    items: items,
                });
            }
            return res;
        });
    }

    async componentDidMount() {
        $(window).on("popstate", (evt) => {
            let state = history.state;
            if (state) {
                this.expandItem(state.id);
            } else {
                this.setState({
                    modal: {
                        isVisible: false,
                        content: null
                    }
                });
            }
        });
        let response = await this.populateGallery();
        if (200 === response.status){
            let url = window.location.pathname;
            let reg = /^\/gallery\/?(\d+)(.*)$/
            let match = reg.exec(url);
            if (match && match[1]) {
                let id = parseInt(match[1]);
                history.replaceState(null, null, "/gallery");
                this.expandItem(id);
            }
        } else {
            console.error(response)
        }
    };

    render() {
        return (
            <div className={"gallery-app"}>
                <div className={"py-5"}>
                    <div className="row">
                        {this.state.items}
                    </div>
                </div>
                <div className={"gallery-app__upload-button"}>
                    <GalleryUpload
                        onTrigger={(files) => {
                            this.triggerUpload(files)
                        }}
                    />
                </div>
                <div className={this.state.modal.isVisible ? "gallery-app__modal container-fluid p-0" : "gallery-app__modal container-fluid p-0 d-none"}>
                    <GalleryModal
                        content={this.state.modal.content}
                        isVisible={this.state.modal.isVisible}
                    />
                </div>
            </div>
        );
    }
}

