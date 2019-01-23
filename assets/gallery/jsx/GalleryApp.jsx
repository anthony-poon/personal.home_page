import React from 'react'
import axios from 'axios';
import GalleryItem from './GalleryItem';
import "./GalleryUpload";
import GalleryUpload from "./GalleryUpload";
import GalleryModal from  "./GalleryModal";
import GalleryViewer from "./GalleryViewer";
import GalleryUploadForm from "./GalleryUploadForm";
import _ from "underscore";
export default class GalleryApp extends React.Component {
    constructor(props) {
        super(props);
        this.storege = [];
        this.obsevers = [];
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

    async modalClose() {
        history.replaceState(null, null, "/gallery");
        this.setState({
            modal: {
                isVisible: false,
                content: null
            }
        });
        let success = await this.queryData();
        if (success) {
            this.renderGalleryItems();
        }
    }

    triggerUpload(files) {
        if (files.length >= 0) {
            this.setState({
                modal: {
                    isVisible: true,
                    content: (
                        <GalleryUploadForm
                            ajaxUrl={this.props.ajaxUrl}
                            uploadFiles={files}
                            modalClose={this.modalClose.bind(this)}
                        />
                    )
                }
            })
        }
    }

    expandItem (id) {
        let item = _.find(this.storege, (v) => {
            return v.id === id;
        });
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
                            assets={item.assets}
                            owner={item.owner}
                            canDelete={item.canDelete}
                            isLiked={item.isLiked}
                            isLoggedIn={this.props.isLoggedIn}
                            signInLink={this.props.signInLink}
                            ajaxUrl={this.props.ajaxUrl}
                            modalClose={this.modalClose.bind(this)}
                        />
                    )
                }
            })
        }
    };

    async queryData() {
        console.log("query");
        this.storege = [];
        let response = await axios.get(this.props.ajaxUrl);
        if (200 === response.status) {
            $.map(response.data, (v, k) => {
                this.storege.push(v);
            });
        } else {
            console.error(response);
        }
        return true;
    }

     renderGalleryItems() {
         console.log("render");
        let items = $.map(this.storege, (value, key) => {
            return (
                <div className={"gallery-item-wrapper"} key={key}>
                    <GalleryItem
                        id={value.id}
                        header={value.header}
                        content={value.content}
                        thumbnail={value.thumbnail}
                        owner={value.owner}
                        onClick={this.expandItem.bind(this)}
                    />
                </div>
            )
        });
        this.setState({
            items: items,
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
        let success = await this.queryData();
        if (success) {
            let url = window.location.pathname;
            let reg = /^\/gallery\/?(\d+)(.*)$/
            let match = reg.exec(url);
            if (match && match[1]) {
                let id = parseInt(match[1]);
                history.replaceState(null, null, "/gallery");
                this.expandItem(id);
            } else {
                this.renderGalleryItems();
            }
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
                        onTrigger={this.triggerUpload.bind(this)}
                    />
                </div>
                <div className={this.state.modal.isVisible ? "gallery-app__modal" : "gallery-app__modal d-none"}>
                    <GalleryModal
                        content={this.state.modal.content}
                        isVisible={this.state.modal.isVisible}
                    />
                </div>
            </div>
        );
    }
}

