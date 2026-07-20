(function () {
  'use strict';

  /* ============================================================
   *  Reproductor de radio persistente
   *  (el <audio> vive fuera de #content, así que la navegación
   *   AJAX de abajo nunca lo recrea ni interrumpe el sonido)
   * ============================================================ */
  var audio = document.getElementById('audioStream');
  var btnPlay = document.getElementById('btnPlay');
  var btnPlayIcon = document.getElementById('btnPlayIcon');
  var liveDot = document.getElementById('liveDot');
  var playerStatus = document.getElementById('playerStatus');
  var volumeRange = document.getElementById('volumeRange');

  function setIcon(playing) {
    btnPlayIcon.classList.toggle('bi-play-fill', !playing);
    btnPlayIcon.classList.toggle('bi-pause-fill', playing);
  }

  function setStatus(text, live) {
    playerStatus.textContent = text;
    liveDot.classList.toggle('on', !!live);
  }

  function iniciarReproduccion() {
    if (!audio || !audio.paused) return;
    btnPlay.disabled = true;
    setStatus('Conectando…', false);
    // Al pausar, el navegador puede haber "cortado" el buffer del stream en vivo.
    // Recargar el <source> asegura que siempre reconecte al punto actual en vivo.
    if (audio.dataset.needsReload === '1') {
      audio.load();
      audio.dataset.needsReload = '0';
    }
    var playPromise = audio.play();
    if (playPromise && playPromise.catch) {
      playPromise.catch(function () {
        btnPlay.disabled = false;
        setStatus('No se pudo conectar, intenta de nuevo', false);
        localStorage.setItem('radioPlaying', '0');
      });
    }
  }

  if (audio) {
    var savedVolume = localStorage.getItem('radioVolume');
    var vol = savedVolume !== null ? parseInt(savedVolume, 10) : 80;
    audio.volume = vol / 100;
    if (volumeRange) volumeRange.value = vol;

    btnPlay.addEventListener('click', function () {
      if (audio.paused) {
        iniciarReproduccion();
      } else {
        audio.pause();
        audio.dataset.needsReload = '1';
      }
    });

    audio.addEventListener('playing', function () {
      btnPlay.disabled = false;
      setIcon(true);
      setStatus('En vivo', true);
      localStorage.setItem('radioPlaying', '1');
    });

    audio.addEventListener('pause', function () {
      btnPlay.disabled = false;
      setIcon(false);
      setStatus('Pausado', false);
      localStorage.setItem('radioPlaying', '0');
    });

    audio.addEventListener('waiting', function () {
      setStatus('Conectando…', false);
    });

    audio.addEventListener('error', function () {
      btnPlay.disabled = false;
      setIcon(false);
      setStatus('Error de conexión, intenta de nuevo', false);
      localStorage.setItem('radioPlaying', '0');
    });

    if (volumeRange) {
      volumeRange.addEventListener('input', function () {
        audio.volume = volumeRange.value / 100;
        localStorage.setItem('radioVolume', volumeRange.value);
      });
    }

    // Si el usuario ya estaba escuchando y el navegador recarga la página
    // completa (F5, enlace externo, etc.) intentamos reanudar automáticamente.
    if (localStorage.getItem('radioPlaying') === '1') {
      setStatus('Conectando…', false);
      var resumePromise = audio.play();
      if (resumePromise && resumePromise.catch) {
        resumePromise.catch(function () {
          setStatus('Presiona play para reanudar', false);
        });
      }
    }
  }

  /* ============================================================
   *  Navegación interna sin recargar la página (AJAX)
   *  Así el reproductor de arriba nunca se detiene al navegar.
   * ============================================================ */
  var contentEl = document.getElementById('content');

  function currentFile() {
    var f = location.pathname.split('/').pop();
    return f === '' ? 'index.php' : f;
  }

  function fileOf(url) {
    var f = url.split('#')[0].split('?')[0].split('/').pop();
    return f === '' ? 'index.php' : f;
  }

  function setActiveNav(url) {
    var clean = fileOf(url);
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
      var rawHref = link.getAttribute('href') || '';
      // Los enlaces de ancla (ej: index.php#redes-section) no son "páginas"
      // propias, así que nunca se marcan como activos por coincidencia de archivo.
      if (rawHref.indexOf('#') !== -1) {
        link.classList.remove('active');
        return;
      }
      link.classList.toggle('active', fileOf(rawHref) === clean);
    });
  }

  function initScrollReveal() {
    var elementos = document.querySelectorAll('.reveal:not(.in-view)');
    if (!elementos.length) return;

    if (!('IntersectionObserver' in window)) {
      elementos.forEach(function (el) { el.classList.add('in-view'); });
      return;
    }

    var observer = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    elementos.forEach(function (el) { observer.observe(el); });
  }

  function initDynamicComponents() {
    if (window.bootstrap && window.bootstrap.Carousel) {
      document.querySelectorAll('.carousel').forEach(function (el) {
        var existing = bootstrap.Carousel.getInstance(el);
        if (existing) existing.dispose();
        new bootstrap.Carousel(el, { interval: 5000, ride: 'carousel' });
      });
    }
    initScrollReveal();
    document.dispatchEvent(new CustomEvent('contentLoaded'));
  }

  function loadPage(rawUrl, pushState) {
    if (!contentEl) {
      window.location.href = rawUrl;
      return;
    }
    var hashIdx = rawUrl.indexOf('#');
    var hash = hashIdx !== -1 ? rawUrl.slice(hashIdx + 1) : '';
    var path = hashIdx !== -1 ? rawUrl.slice(0, hashIdx) : rawUrl;
    if (path === '') path = 'index.php';
    var sep = path.indexOf('?') !== -1 ? '&' : '?';

    contentEl.classList.add('loading');
    fetch(path + sep + 'ajax=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        var title = res.headers.get('X-Page-Title');
        return res.text().then(function (html) { return { html: html, title: title }; });
      })
      .then(function (data) {
        contentEl.innerHTML = data.html;
        if (data.title) document.title = data.title;
        if (pushState) history.pushState({}, '', rawUrl);
        setActiveNav(rawUrl);
        initDynamicComponents();
        if (hash) {
          var target = document.getElementById(hash);
          if (target) target.scrollIntoView({ behavior: 'smooth' });
        } else {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      })
      .catch(function () {
        window.location.href = rawUrl;
      })
      .finally(function () {
        contentEl.classList.remove('loading');
      });
  }

  document.addEventListener('click', function (e) {
    var link = e.target.closest('[data-ajax-link]');
    if (!link) return;
    if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;

    var url = link.getAttribute('href');
    if (!url) return;

    var hashIdx = url.indexOf('#');
    var pathPart = hashIdx !== -1 ? url.slice(0, hashIdx) : url;
    var hashPart = hashIdx !== -1 ? url.slice(hashIdx + 1) : '';

    // Ancla dentro de la misma página ya cargada: solo hacemos scroll.
    if (hashPart && (pathPart === '' || fileOf(pathPart) === currentFile())) {
      var target = document.getElementById(hashPart);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth' });
        history.pushState({}, '', url);
        if (link.hasAttribute('data-play-trigger')) {
          iniciarReproduccion();
        }
        return;
      }
    }

    e.preventDefault();
    loadPage(url, true);
  });

  window.addEventListener('popstate', function () {
    loadPage(location.pathname + location.search + location.hash, false);
  });

  document.addEventListener('DOMContentLoaded', function () {
    setActiveNav(location.pathname);
    initDynamicComponents();
  });

  /* ============================================================
   *  Navbar dinámico: se contrae al hacer scroll y anima el
   *  botón hamburguesa (líneas -> X) al abrir/cerrar el menú móvil.
   * ============================================================ */
  var siteHeader = document.querySelector('.site-header');
  var hamburgerBtn = document.querySelector('.hamburger-btn');
  var navMenu = document.getElementById('navMenu');

  if (siteHeader) {
    var onScroll = function () {
      siteHeader.classList.toggle('scrolled', window.scrollY > 40);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  if (hamburgerBtn && navMenu) {
    navMenu.addEventListener('shown.bs.collapse', function () {
      hamburgerBtn.classList.add('open');
      actualizarAltosLayout();
    });
    navMenu.addEventListener('hidden.bs.collapse', function () {
      hamburgerBtn.classList.remove('open');
      actualizarAltosLayout();
    });
  }

  /* ============================================================
   *  Mide el alto real del navbar y del reproductor (en vez de
   *  adivinarlo en el CSS) para que el slider del home encaje
   *  exacto entre ambos, sin dejar un hueco ni quedar tapado.
   * ============================================================ */
  var radioPlayerEl = document.getElementById('radioPlayer');

  function actualizarAltosLayout() {
    if (siteHeader) {
      document.documentElement.style.setProperty('--header-h', siteHeader.offsetHeight + 'px');
    }
    if (radioPlayerEl) {
      document.documentElement.style.setProperty('--player-h', radioPlayerEl.offsetHeight + 'px');
    }
  }

  actualizarAltosLayout();
  window.addEventListener('load', actualizarAltosLayout);
  window.addEventListener('resize', actualizarAltosLayout);
  if (siteHeader) {
    siteHeader.addEventListener('transitionend', actualizarAltosLayout);
  }
})();
