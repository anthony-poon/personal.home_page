import React from 'react'

export default class GalleryItem extends React.Component{
    constructor(props) {
        super(props);
        console.log(props);
        this.unlikeBtn =
            <button type={"button"} className="btn btn-outline-primary btn-sm border-0" onClick={() => {
                this.onLikeBtnClick()
            }}>
                <i className="fas fa-heart"/>
            </button>;
        this.likeBtn =
            <button type={"button"} className="btn btn-outline-primary btn-sm border-0" onClick={() => {
                this.onLikeBtnClick()
            }}>
                <i className="far fa-heart"/>
            </button>;
        if (props.isLiked) {
            this.state = ({
                likeBtn: this.unlikeBtn
            })
        } else {
            this.state = ({
                likeBtn: this.likeBtn
            })
        }

    }
    onLikeBtnClick() {
        if (this.props.isLoggedIn) {
            if (this.props.isLiked) {
                this.props.onUnlike({
                    id: this.props.id
                });
                this.setState({
                    likeBtn: this.likeBtn
                })
            } else {
                this.props.onLike({
                    id: this.props.id
                });
                this.setState({
                    likeBtn: this.unlikeBtn
                })
            }
        } else {
            this.setState({
                likeBtn: <a href={this.props.signInLink} className={"text-primary btn-like px-2"}>Sign In</a>
            })
        }
    }
    render() {
        return (
            <div className={"gallery-item"}>
                <div className={"gallery-img-container btn-like"} onClick={() => {
                    this.props.onExpand(this.props.id)
                }}>
                    <img className={"gallery-img"} src={this.props.thumbnail}/>
                </div>
                <div className={"gallery-body"}>
                    <div className={"gallery-header"}>
                        {this.props.header}
                    </div>
                    <div className={"gallery-bar mt-3 row"}>
                        <div className={"col-auto"}>
                            {this.state.likeBtn}
                            <button type={"button"} className="btn btn-outline-primary btn-sm border-0">
                                <i className="fas fa-share-alt"/>
                            </button>
                        </div>
                        <div className={"col text-right"}>
                            <button type={"button"} className="btn btn-outline-danger btn-sm border-0" onClick={() => {
                                this.props.onDelete({
                                    id: this.props.id
                                })
                            }}>
                                <i className="fas fa-trash-alt"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}