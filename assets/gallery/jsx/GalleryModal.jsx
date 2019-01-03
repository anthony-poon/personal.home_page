import React from "react";

export default class GalleryModal extends React.Component{
    constructor(props) {
        super(props);
    }

    btnClose() {
    }

    render() {
        if (this.props.isVisible) {
            $("body").css("overflow-y", "hidden");
        } else {
            $("body").css("overflow-y", "auto");
        }
        return (
            <div className={this.props.isVisible ? "gallery-modal" : " gallery-modal d-none"}>
                <div className={"gallery-modal__wrapper"}>
                    {this.props.content}
                </div>
            </div>
        );
    }
};