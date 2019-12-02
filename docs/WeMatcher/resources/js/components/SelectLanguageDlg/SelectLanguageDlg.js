import React, { Component, forwardRef } from 'react';
import ReactDOM from 'react-dom';
import { withStyles , makeStyles } from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Radio from '@material-ui/core/Radio';
import RadioGroup from '@material-ui/core/RadioGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormControl from '@material-ui/core/FormControl';
import FormLabel from '@material-ui/core/FormLabel';
import DialogTitle from '@material-ui/core/DialogTitle';
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import Switch from '@material-ui/core/Switch';
import * as Constants from '../../constants';
import { withTranslation, I18nextProvider } from "react-i18next";
import i18n from "../i18n";

const styles = theme => ({
  dlg:{
      zIndex: 1200000,
      width: 'fit-content',
  },
  radiogroup:{
      maxHeight: 400
  },
  item: {
      fontSize: 12,
      height: 20,
      margin: theme.spacing(1)
  },
  button: {
    margin: theme.spacing(1),
  },
  input: {
    display: 'none',
  },
  formControl: {
      margin: theme.spacing(3),
  },
});

class SelectLanguageDlg extends Component {
  constructor(props) {
    super(props);

    if(this.props.lang){
      i18n.changeLanguage(this.props.lang);
    }

    this.state = {
      ...props,
      translate: {
        auto: props.translate.auto,
        lang: props.translate.lang
      },
      open : props.open
    }

    this.handleChange = this.handleChange.bind(this)
    this.handleClose = this.handleClose.bind(this)
    this.openDlg = this.openDlg.bind(this)
    this.handleLangChange = this.handleLangChange.bind(this)
  }

  handleChange = event => {
    this.setState({ ...this.state, translate: { ...this.state.translate, auto: event.target.checked }});
  };

  handleClose = () => {
    if(!this.props.onClose){
      var url = this.props.translateconfurl;
      url = url.replace('#lang#', this.state.translate.lang);
      url = url.replace('#auto#', this.state.translate.auto);
      window.location = url;
    }
    else{
      this.props.onClose(this.state.translate);
    }
  };

  openDlg() {
    this.setState({ ...this.state, open: true });
  }

  handleLangChange = event => {
    this.setState({ ...this.state, translate: { ...this.state.translate, lang: event.target.value }});
  };

  componentDidMount() {
  }

  render() {

    const {t, classes} = this.props

    return (
      <Dialog onClose={this.handleClose} aria-labelledby="simple-dialog-title" open={this.props.onClose ? this.props.open : this.state.open}>
        <DialogTitle id="simple-dialog-title">{t("Translation setting")}</DialogTitle>
        <DialogContent>
          <div className="auto-translate clearfix">
                  <label className="font-weight-bold">{t("Auto-translate")}</label>
                  <Switch
                      checked={this.state.translate.auto}
                      onChange={this.handleChange}
                      inputProps={{ 'aria-label': 'primary checkbox' }}
                  />
              </div>
              <FormControl component="fieldset" className={classes.formControl}>
                  <FormLabel component="legend">{t("translate message to")}</FormLabel>
                  <RadioGroup aria-label="lang" name="lang" value={this.state.translate.lang} onChange={this.handleLangChange} className={classes.radiogroup}>
                      {Object.keys(Constants.LANG).map((key) =>(
                          <FormControlLabel key={key} value={key} control={<Radio className={classes.item}/>} label={Constants.LANG[key]} />
                      ))}
                  </RadioGroup>
              </FormControl>
              <Button variant="contained" color="primary" className={classes.button} onClick={this.handleClose}>
                  {t("Apply")}
              </Button>
          </DialogContent>
      </Dialog>
    );
  }

}

export default withStyles(styles)(SelectLanguageDlg);

const SelectLanguageDlg_ = withTranslation('translations', { withRef: true })(withStyles(styles)(SelectLanguageDlg));

const SelectLanguageDlg__ = forwardRef((props, ref) => {
  return (<I18nextProvider i18n={i18n}>
            <SelectLanguageDlg_ {...props} ref={ref}/>
          </I18nextProvider>);
});



var component = document.getElementById('select_language_dlg');
if(component){
  var props = Object.assign({}, component.dataset);

  if(props.translateauto == "true" || props.translateauto == "1"){
    props.translateauto = true;
  }
  else{
    props.translateauto = false;
  }

  props = {
    ...props,
    translate: {
      auto: props.translateauto,
      lang: props.translatelang
    }
  }

  ReactDOM.render(
      <SelectLanguageDlg__ {...(props)} open={false} ref={(selectlanguageComponent) => {window.selectlanguageComponent = selectlanguageComponent}}/>
      , component);
}
