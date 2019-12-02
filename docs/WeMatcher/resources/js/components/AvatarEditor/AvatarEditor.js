import React from 'react'
import ReactDOM from 'react-dom'
import Avatar from 'react-avatar-edit'
import swal from 'sweetalert';
import { withTranslation, I18nextProvider } from "react-i18next";
import i18n from "../i18n";

class AvatarEditor extends React.Component {

  constructor(props) {
    super(props)

    i18n.changeLanguage(this.props.lang);
    this.state = {
      preview: '',
    }
    this.onCrop = this.onCrop.bind(this)
    this.onClose = this.onClose.bind(this)
    this.onBeforeFileLoad = this.onBeforeFileLoad.bind(this)
  }
  
  onClose() {
    this.setState({preview: ''})
  }
  
  onCrop(preview) {
    this.setState({preview})
  }

  onBeforeFileLoad(elem) {
    let that = this;
    if(elem.target.files[0].size > 1071680){
      swal({
        icon: 'error',
        title: 'Oops...',
        text: that.props.t('File is too big!')
      })
      elem.target.value = "";
    };
  }

  render () {
    return (
      <div>
        <Avatar
          width={$('#avatar_container').width()}
          height={$('#avatar_container').width()}
          imageWidth={150}
          imageHeight={150}
          onCrop={this.onCrop}
          onClose={this.onClose}
          onBeforeFileLoad={this.onBeforeFileLoad}
          src={this.props.src}
        />
        <input type="hidden" name="avatar" value={this.state.preview}/>
      </div>
    )
  }
}

const AvatarEditor_ = withTranslation()(AvatarEditor);


document.querySelectorAll('.avatareditor').forEach(function(component) {

  var props = Object.assign({}, component.dataset);
  ReactDOM.render(
    <I18nextProvider i18n={i18n}>
      <AvatarEditor_ {...(props)}/>
    </I18nextProvider>, document.querySelector('.avatareditor'))

})