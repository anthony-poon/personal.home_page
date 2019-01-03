import React from 'react'

export default class GalleryItem extends React.Component{
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <div className={"gallery-item"}>
                <div className={"gallery-img-container btn-like"} onClick={(evt) => {
                    this.props.onClick(evt, this.props.id)
                }}>
                    <img className={"gallery-img"} src={this.props.assets[0]}/>
                </div>
                <div className={"gallery-body"}>
                    <div className={"gallery-header"}>
                        {this.props.header}
                    </div>
                    <div className={"gallery-bar mt-3"}>
                        <button type={"button"} className="btn btn-outline-primary btn-sm border-0">
                            <i className="far fa-heart"/>
                        </button>
                        <button type={"button"} className="btn btn-outline-primary btn-sm border-0">
                            <i className="fas fa-share-alt"/>
                        </button>
                    </div>
                </div>
            </div>
        );
    }
}