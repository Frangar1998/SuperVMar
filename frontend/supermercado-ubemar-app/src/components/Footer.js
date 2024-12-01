import React from 'react';
import { Link } from 'react-router-dom';
import { BsFacebook, BsInstagram } from 'react-icons/bs';

const Footer = () => {
    return (<>
        <footer className="py-4">
            <div className="container-xxl">
                <div className="row align-items-center">
                    <div className="col-5">
                        <div className="footer-top-data d-flex gap-30 align-items-center">
                            <img src="images/newsletter.png" alt="newsletter" />
                            <h2 className="mb-0 text-white">Suscríbete a la Newsletter</h2>
                        </div>
                    </div>
                    <div className="col-7">
                    <div className="input-group">
                            <input type="text" className="form-control py-1" placeholder="Email" aria-label="Email" aria-describedby="basic-addon2" />
                            <span className="input-group-text p-2" id="basic-addon2">
                                Suscribirse
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <footer className="py-4">
            <div className="container-xxl">
                <div className="row">
                    <div className="col-4">
                        <h4 className="text-white mb-4">Contáctanos</h4>
                        <div>
                            <address className="text-white fs-6">
                                Carretera de Níjar 294 <br />
                                La Cañada de San Urbano, Almería <br />
                                CP: 04120 <br />
                                
                            </address>
                            <a href="tel:664788445" className="mt-3 d-block mb-1 text-white">
                                +34 664 788 445
                            </a>
                            <a href="mailto:franky23398@gmail.com" className="mt-2 d-block mb-0 text-white">
                                ubemar@superubemar.com
                            </a>
                            <div className="social-icons d-flex align-items-center gap-15 mt-4">
                                <a className="text-white" href="/">
                                    <BsFacebook className="fs-2" />
                                </a>
                                <a className="text-white" href="/">
                                    <BsInstagram className="fs-2" />
                                </a>
                            </div>
                        </div>
                    </div>
                    <div className="col-3">
                        <h4 className="text-white mb-4">Información</h4>
                        <div>
                            <div className="footer-links d-flex flex-column">
                                <Link className="text-white py-2 mb-1">Política de privacidad</Link>
                                <Link className="text-white py-2 mb-1">Política de devoluciones</Link>
                                <Link className="text-white py-2 mb-1">Política de pedidos</Link>
                                <Link className="text-white py-2 mb-1">Términos y condiciones</Link>
                            </div>
                        </div>
                    </div>
                    <div className="col-3">
                        <h4 className="text-white mb-4">Cuenta</h4>
                        <div>
                            <div className="footer-links d-flex flex-column">
                                <Link className="text-white py-2 mb-1">Sobre nosotros</Link>
                                <Link className="text-white py-2 mb-1">Preguntas frecuentes</Link>
                                <Link className="text-white py-2 mb-1">Contacto</Link>
                            </div>
                        </div>
                    </div>
                    <div className="col-2">
                        <h4 className="text-white mb-4">Secciones</h4>
                        <div className="footer-links d-flex flex-column">
                            <Link className="text-white py-2 mb-1">Carnicería</Link>
                            <Link className="text-white py-2 mb-1">Charcutería</Link>
                            <Link className="text-white py-2 mb-1">Frutería</Link>
                            <Link className="text-white py-2 mb-1">Alimentación</Link>
                            <Link className="text-white py-2 mb-1">Hogar</Link>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <footer className="py-4">
            <div className="container-xxl">
                <div className="row">
                    <div className="col-12">
                        <p className="text-center mb-0 text-white">
                            &copy; {new Date().getFullYear()} Desarrollado por Francisco Jesús García López
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </>
    );
};

export default Footer;