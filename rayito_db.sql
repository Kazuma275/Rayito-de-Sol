-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 30-03-2025 a las 21:13:41
-- Versión del servidor: 10.11.11-MariaDB
-- Versión de PHP: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rayito_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact`
--

CREATE TABLE `contact` (
  `contact_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contact`
--

INSERT INTO `contact` (`contact_id`, `customer_name`, `customer_email`, `message`, `created_at`) VALUES
(9, 'Alma', 'sergioramos@gmail.com', '1', '2024-12-05 09:36:25'),
(10, 'Alma', 'sergioramos@gmail.com', '21', '2024-12-05 12:46:12'),
(11, 'hola', 'porfa@tetas.com', 'que tal!!!', '2024-12-05 23:11:12'),
(12, 'putoSergio', 'sergio@sdkfh.df', 'no', '2024-12-12 11:39:49'),
(13, 'Alma', 'sergioramos@gmail.com', '12', '2024-12-12 19:37:37'),
(14, 'Alma', 'alma@gmail.com', 'Un lolete', '2024-12-16 08:26:17'),
(15, 'King Africa', 'gonzalo@gmail.com', 'Hola NO soy gonzalo', '2024-12-16 08:29:20'),
(16, 'sergio', 'sergioramos@gmail.com', '1234', '2024-12-16 08:45:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservations`
--

INSERT INTO `reservations` (`id`, `username`, `reservation_date`, `reservation_time`, `created_at`, `updated_at`) VALUES
(8, 'sergop', '2237-12-06', '02:00:00', '2024-12-05 23:10:47', '2024-12-05 23:10:47'),
(9, 'sergio', '4444-03-21', '12:03:00', '2024-12-12 19:37:07', '2024-12-12 19:37:07'),
(10, 'xczxcxz', '2024-12-13', '09:56:00', '2024-12-13 08:55:08', '2024-12-13 08:55:08'),
(11, 'https', '2024-12-01', '17:49:00', '2024-12-13 15:47:47', '2024-12-13 15:47:47'),
(12, 'alvaro', '2005-12-12', '20:12:00', '2024-12-16 08:25:39', '2024-12-16 08:25:39'),
(13, 'KingAfrica', '2001-09-11', '12:00:00', '2024-12-16 08:29:52', '2024-12-16 08:29:52'),
(14, 'bruce', '2024-12-20', '12:30:00', '2024-12-16 08:30:59', '2024-12-16 08:30:59'),
(15, 'Gerard', '2025-03-29', '22:36:00', '2025-01-16 21:36:17', '2025-01-16 21:36:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `author_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `review_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `author_image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `review_date` timestamp NULL DEFAULT current_timestamp(),
  `language` varchar(5) NOT NULL DEFAULT 'en'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reviews`
--

INSERT INTO `reviews` (`review_id`, `author_name`, `review_text`, `author_image`, `review_date`, `language`) VALUES
(1, 'Isabel', 'Experiencia inolvidable!! Este apartamento es simplemente un sueño hecho realidad. Las vistas al mar desde la cama son espectaculares; despertar con el sonido de las olas y ver los primeros rayos del sol en el horizonte es una experiencia mágica. Al caer la tarde, los atardeceres crean un ambiente increíblemente romántico, ideal para desconectar y disfrutar del entorno. No hay nada como relajarse en este espacio tan acogedor mientras observas cómo el mar cambia de colores a lo largo del día. Perfecto para una escapada tranquila y especial. ¡Altamente recomendado!', 'https://xx.bstatic.com/static/img/review/avatars/ava-i.png', '2024-08-31 22:00:00', 'es'),
(2, 'Gisele', 'Muy buena\nPuntuación: 9,0\nLa llegada fue fácil, por WhatsApp recibimos indicaciones. Como booking dice que uno se debe comunicar solo a través de la página, me llamó la atención pero todo funcionó bien.\nEl departamento es cómodo, limpio y seguro. Bella vista al mar y queda cerca de un centro comercial y hay donde aparcar. Nuestra estadía nos gustó.', 'https://xx.bstatic.com/static/img/review/avatars/ava-g.png', '2024-09-30 22:00:00', 'es'),
(3, 'Karolaing', 'Excepcional las vistas al mar. Puntuación: 10/10. ', 'https://xx.bstatic.com/static/img/review/avatars/ava-k.png', '2024-10-04 22:00:00', 'es'),
(4, 'Eli', 'Fantástico. Puntuación: 9,0. María José muy atenta y amable. El apartamento perfecto, tiene todo lo necesario para estar cómodo. La ubicación inmejorable, todos los lugares de interés entre media hora a una hora de camino. El restaurante El Capricho, que está allí mismo es una pasada para comer. Le falta una terracita al apartamento.', 'https://xx.bstatic.com/static/img/review/avatars/ava-e.png', '2024-07-31 22:00:00', 'es'),
(5, 'Pablo', 'Estancia muy agradable. Puntuación: 9,0. La estancia ha sido muy agradable. El apartamento, limpios y muy cómodo, con vistas impresionantes. Sin duda, recomiendo este alojamiento para una experiencia inolvidable. Es difícil aparcar en determinadas horas del día.', 'https://xx.bstatic.com/static/img/review/avatars/ava-p.png', '2024-07-31 22:00:00', 'es'),
(6, 'Roman', 'Fantástico. Puntuación: 9,0. Great view from the apartment. Communication with the owner, at our request we were already accommodated at 12:00. General appearance of the house in which the apartment was located.', 'https://xx.bstatic.com/static/img/review/avatars/ava-r.png', '2024-10-31 23:00:00', 'cs'),
(7, 'Rachel', 'Lovely and would use again. Rating: 10. Very clean, good location, everything you need. Clean and well kept.', 'https://xx.bstatic.com/static/img/review/avatars/ava-r.png', '2024-09-30 22:00:00', 'en'),
(8, 'Nadine', 'Exceptional. Rating: 10. Fantastic modern clean apartment. The air conditioning was working particularly well.', 'https://xx.bstatic.com/static/img/review/avatars/ava-n.png', '2024-07-31 22:00:00', 'en'),
(9, 'Natalia', 'Super apartamento. Puntuación: 10. Apartamento precioso. Muy buen contacto con la propietaria, vista impresionante. En el apartamento había todo lo necesario.', 'https://xx.bstatic.com/static/img/review/avatars/ava-n.png', '2024-09-30 22:00:00', 'es'),
(10, 'Massimo', 'Hermosa vista al mar, pero el calor intenso de la habitación es un inconveniente. Puntuación: 8,0. El apartamento es muy bonito, está en el 9° piso, por lo que tiene una hermosa vista al mar, y está equipado con todo lo necesario para la estancia. La piscina comunitaria también es agradable. Las ventanas en el balcón calientan mucho todo el apartamento durante todo el día hasta que se pone el sol, y los acondicionadores de aire, desafortunadamente, no enfrían lo suficiente. La lavadora que no funcionaba fue un problema.', 'https://xx.bstatic.com/static/img/review/avatars/ava-m.png', '2024-08-31 22:00:00', 'es'),
(11, 'Zotov', 'Nos gustó todo. Además, la anfitriona nos proporcionó una cafetera por iniciativa propia, aunque no estaba en los servicios. Hubo un pequeño problema con el internet, pero se solucionó bastante rápido.', 'https://xx.bstatic.com/static/img/review/avatars/ava-z.png', '2024-06-30 22:00:00', 'es'),
(12, 'Carlos', 'Excepcional', 'https://xx.bstatic.com/static/img/review/avatars/ava-c.png', '2024-07-31 22:00:00', 'es'),
(13, 'Alejandro', 'Excepcional', 'https://xx.bstatic.com/static/img/review/avatars/ava-a.png', '2024-06-30 22:00:00', 'es'),
(14, 'Olesia', 'Excepcional', 'https://xx.bstatic.com/static/img/review/avatars/ava-o.png', '2024-10-31 23:00:00', 'es'),
(15, 'Bahamut899', 'Fantástico', 'https://xx.bstatic.com/static/img/review/avatars/ava-b.png', '2024-08-31 22:00:00', 'es');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `review_translations`
--

CREATE TABLE `review_translations` (
  `translation_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `review_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `review_translations`
--

INSERT INTO `review_translations` (`translation_id`, `review_id`, `language_code`, `review_text`) VALUES
(1, 1, 'es', 'Experiencia inolvidable!! Este apartamento es simplemente un sueño hecho realidad. Las vistas al mar desde la cama son espectaculares; despertar con el sonido de las olas y ver los primeros rayos del sol en el horizonte es una experiencia mágica. Al caer la tarde, los atardeceres crean un ambiente increíblemente romántico, ideal para desconectar y disfrutar del entorno. No hay nada como relajarse en este espacio tan acogedor mientras observas cómo el mar cambia de colores a lo largo del día. Perfecto para una escapada tranquila y especial. ¡Altamente recomendado!'),
(2, 1, 'en', 'Unforgettable experience! This apartment is simply a dream come true. The sea views from the bed are spectacular; waking up to the sound of the waves and seeing the first rays of the sun on the horizon is a magical experience. In the evening, the sunsets create an incredibly romantic atmosphere, perfect for disconnecting and enjoying the surroundings. There is nothing like relaxing in this cozy space while watching the sea change colors throughout the day. Perfect for a peaceful and special getaway. Highly recommended!'),
(3, 1, 'fr', 'Expérience inoubliable ! Cet appartement est tout simplement un rêve devenu réalité. Les vues sur la mer depuis le lit sont spectaculaires ; se réveiller au son des vagues et voir les premiers rayons du soleil à l\'horizon est une expérience magique. En soirée, les couchers de soleil créent une atmosphère incroyablement romantique, idéale pour se déconnecter et profiter des environs. Il n\'y a rien de tel que de se détendre dans cet espace cosy tout en observant la mer changer de couleur tout au long de la journée. Parfait pour une escapade tranquille et spéciale. Fortement recommandé !'),
(4, 1, 'cn', '难忘的经历！这间公寓简直是梦想成真。从床上看到的大海景色令人惊叹；听着海浪声醒来，看到地平线上第一缕阳光是一种神奇的体验。傍晚时分，日落创造了一个非常浪漫的氛围，非常适合放松和享受周围环境。没有什么比在这个温馨的空间里放松，同时欣赏海洋在一天中的颜色变化更好了。非常适合安静而特别的度假。强烈推荐！'),
(5, 1, 'br', 'Experiência inesquecível! Este apartamento é simplesmente um sonho que se tornou realidade. A vista para o mar da cama é espetacular; acordar com o som das ondas e ver os primeiros raios de sol no horizonte é uma experiência mágica. À noite, os pôr do sol criam uma atmosfera incrivelmente romântica, perfeita para desconectar e aproveitar o ambiente. Não há nada como relaxar neste espaço acolhedor enquanto observa o mar mudando de cor ao longo do dia. Perfeito para uma escapada tranquila e especial. Altamente recomendado!'),
(6, 1, 'it', 'Esperienza indimenticabile! Questo appartamento è semplicemente un sogno che diventa realtà. La vista sul mare dal letto è spettacolare; svegliarsi con il suono delle onde e vedere i primi raggi di sole all\'orizzonte è un\'esperienza magica. Alla sera, i tramonti creano un\'atmosfera incredibilmente romantica, ideale per disconnettersi e godersi i dintorni. Non c\'è niente di meglio che rilassarsi in questo spazio accogliente mentre si osserva il mare cambiare colore durante il giorno. Perfetto per una fuga tranquilla e speciale. Altamente raccomandato!'),
(7, 1, 'ua', 'Незабутній досвід! Ця квартира – просто мрія, що стала реальністю. Вид на море з ліжка вражає; прокидатися під звук хвиль і бачити перші промені сонця на горизонті – це магічне відчуття. Увечері заходи сонця створюють неймовірно романтичну атмосферу, ідеальну для відпочинку та насолоди оточенням. Немає нічого кращого, ніж розслабитися в цьому затишному просторі, спостерігаючи, як море змінює кольори протягом дня. Ідеально для спокійного та особливого відпочинку. Рекомендується!'),
(8, 1, 'ru', 'Незабвенное впечатление! Эта квартира – просто мечта, ставшая реальностью. Вид на море с кровати потрясающий; просыпаться под звук волн и видеть первые лучи солнца на горизонте – это волшебное ощущение. Вечером закаты создают невероятно романтичную атмосферу, идеально подходящую для того, чтобы отключиться и наслаждаться окружающей обстановкой. Нет ничего лучше, чем расслабиться в этом уютном пространстве, наблюдая, как море меняет цвет в течение дня. Идеально для спокойного и особенного отдыха. Настоятельно рекомендую!'),
(9, 2, 'es', 'Muy buena. Puntuación: 9,0. La llegada fue fácil, por WhatsApp recibimos indicaciones. Como booking dice que uno se debe comunicar solo a través de la página, me llamó la atención pero todo funcionó bien. El departamento es cómodo, limpio y seguro. Bella vista al mar y queda cerca de un centro comercial y hay donde aparcar. Nuestra estadía nos gustó.'),
(10, 2, 'en', 'Very good. Rating: 9.0. The arrival was easy; we received directions via WhatsApp. Since booking says we should communicate only through the website, it caught my attention, but everything worked out well. The apartment is comfortable, clean, and safe. Beautiful sea view and it is close to a shopping center, and there is parking available. We enjoyed our stay.'),
(11, 2, 'fr', 'Très bien. Note : 9,0. L\'arrivée a été facile, nous avons reçu des indications par WhatsApp. Comme booking dit que l\'on doit communiquer uniquement par le site, cela m\'a interpellé, mais tout s\'est bien passé. L\'appartement est confortable, propre et sécurisé. Belle vue sur la mer et il est proche d\'un centre commercial, avec possibilité de se garer. Nous avons apprécié notre séjour.'),
(12, 2, 'cn', '很好. 评分：9.0. 到达很方便，我们通过 WhatsApp 收到指示。由于预订说只能通过网站进行沟通，这引起了我的注意，但一切都顺利进行。公寓舒适、干净、安全。美丽的海景，靠近购物中心，还有停车位可用。我们很喜欢我们的住宿。'),
(13, 2, 'br', 'Muito bom. Nota: 9,0. A chegada foi fácil, recebemos indicações pelo WhatsApp. Como o booking diz que devemos nos comunicar apenas pelo site, chamou minha atenção, mas tudo funcionou bem. O apartamento é confortável, limpo e seguro. Bela vista para o mar e fica perto de um shopping center, além de ter onde estacionar. Gostamos da nossa estadia.'),
(14, 2, 'it', 'Molto buono. Voto: 9,0. L\'arrivo è stato facile, abbiamo ricevuto indicazioni tramite WhatsApp. Poiché Booking dice che bisogna comunicare solo attraverso il sito, mi ha colpito, ma tutto è andato bene. L\'appartamento è comodo, pulito e sicuro. Bella vista sul mare e vicino a un centro commerciale, con possibilità di parcheggio. Abbiamo apprezzato il nostro soggiorno.'),
(15, 2, 'ua', 'Дуже добре. Оцінка: 9,0. Приїзд був легким, ми отримали вказівки через WhatsApp. Оскільки на Booking кажуть, що потрібно спілкуватися тільки через сайт, це мене здивувало, але все пройшло добре. Квартира зручна, чиста та безпечна. Гарний вид на море і недалеко від торгового центру, є місце для паркування. Нам сподобалося наше перебування.'),
(16, 2, 'ru', 'Очень хорошо. Оценка: 9,0. Прибытие было легким, мы получили указания через WhatsApp. Поскольку на Booking говорится, что нужно общаться только через сайт, это меня удивило, но все прошло хорошо. Квартира удобная, чистая и безопасная. Прекрасный вид на море, недалеко от торгового центра, есть место для парковки. Нам понравилось наше пребывание.'),
(17, 3, 'es', 'Excepcional. Puntuación: 10/10. Las vistas al mar y la tranquilidad del lugar hicieron de nuestra estancia una experiencia inolvidable. El apartamento es moderno, bien equipado y con una decoración elegante. Perfecto para disfrutar de unas vacaciones de descanso y comodidad. Muy recomendado.'),
(18, 3, 'en', 'Exceptional. Rating: 10/10. The sea views and the tranquility of the place made our stay an unforgettable experience. The apartment is modern, well-equipped, and has an elegant decoration. Perfect for enjoying a relaxing and comfortable vacation. Highly recommended.'),
(19, 3, 'fr', 'Exceptionnel. Note : 10/10. Les vues sur la mer et la tranquillité des lieux ont rendu notre séjour inoubliable. L\'appartement est moderne, bien équipé et sécurisé. Parfait pour profiter de vacances reposantes et confortables. Très recommandé.'),
(20, 3, 'cn', '卓越。评分：10/10。海景和环境的宁静让我们的住宿成为难忘的经历。公寓现代化，设备齐全，装饰优雅。非常适合享受轻松舒适的假期。强烈推荐。'),
(21, 3, 'br', 'Excepcional. Nota: 10/10. As vistas para o mar e a tranquilidade do lugar tornaram nossa estadia inesquecível. O apartamento é moderno, bem equipado e tem uma decoração elegante. Perfeito para desfrutar de férias relaxantes e confortáveis. Altamente recomendado.'),
(22, 3, 'it', 'Eccezionale. Voto: 10/10. Le viste sul mare e la tranquillità del luogo hanno reso il nostro soggiorno indimenticabile. L\'appartamento è moderno, ben attrezzato e decorato con eleganza. Perfetto per godersi una vacanza rilassante e confortevole. Molto raccomandato.'),
(23, 3, 'ua', 'Винятковий. Оцінка: 10/10. Вид на море та спокійне місце зробили наше перебування незабутнім досвідом. Квартира сучасна, добре обладнана та елегантно оформлена. Ідеально підходить для відпочинку та комфортної відпустки. Дуже рекомендую.'),
(24, 3, 'ru', 'Исключительно. Оценка: 10/10. Вид на море и спокойствие этого места сделали наше пребывание незабываемым. Квартира современная, хорошо оборудованная и элегантно оформленная. Отлично подходит для комфортного и расслабляющего отдыха. Очень рекомендую.'),
(25, 4, 'es', 'Fantástico. Puntuación: 9,0. María José muy atenta y amable. El apartamento perfecto, tiene todo lo necesario para estar cómodo. La ubicación inmejorable, todos los lugares de interés entre media hora a una hora de camino. El restaurante El Capricho, que está allí mismo es una pasada para comer. Le falta una terracita al apartamento.'),
(26, 4, 'en', 'Fantastic. Rating: 9.0. María José was very attentive and friendly. The apartment was perfect, with everything needed to be comfortable. The location was unbeatable, with all the points of interest within a half-hour to one hour drive. The restaurant El Capricho, located there, is amazing for dining. The apartment could use a small terrace.'),
(27, 4, 'fr', 'Fantastique. Note : 9,0. María José était très attentionnée et sympathique. L\'appartement était parfait, avec tout le nécessaire pour être à l\'aise. L\'emplacement était imbattable, tous les lieux d\'intérêt à moins d\'une demi-heure à une heure de route. Le restaurant El Capricho, qui est sur place, est excellent pour manger. L\'appartement aurait besoin d\'une petite terrasse.'),
(28, 4, 'cn', '极好。评分：9,0。玛丽亚·何塞非常细心和友好。公寓很完美，拥有一切必要的设施以保持舒适。地理位置无可挑剔，所有景点都在半小时到一小时的车程内。位于此处的餐厅El Capricho是吃饭的好去处。公寓需要一个小阳台。'),
(29, 4, 'br', 'Fantástico. Nota: 9,0. María José foi muito atenciosa e simpática. O apartamento era perfeito, com tudo o que é necessário para estar confortável. A localização era imbatível, com todos os pontos de interesse a meia hora a uma hora de carro. O restaurante El Capricho, que fica lá mesmo, é ótimo para comer. O apartamento precisaria de um pequeno terraço.'),
(30, 4, 'it', 'Fantastico. Voto: 9,0. María José è stata molto attenta e amichevole. L\'appartamento era perfetto, con tutto il necessario per stare comodi. La posizione era impareggiabile, con tutti i punti di interesse a mezz\'ora a un\'ora di auto. Il ristorante El Capricho, che si trova lì, è fantastico per mangiare. L\'appartamento potrebbe beneficiare di una piccola terrazza.'),
(31, 4, 'ua', 'Фантастично. Оцінка: 9,0. Марія Хосе була дуже уважною та привітною. Апартаменти були ідеальні, з усім необхідним для комфорту. Розташування бездоганне, усі основні місця знаходяться в півгодини-годину їзди. Ресторан El Capricho, що знаходиться поруч, чудовий для обіду. Апартаменту не вистачає маленької тераси.'),
(32, 4, 'ru', 'Фантастически. Оценка: 9,0. Мария Хосе была очень внимательной и дружелюбной. Квартира была идеальной, с всем необходимым для комфорта. Расположение непревзойденное, все интересные места находятся в получасе-часе езды. Ресторан El Capricho, который находится рядом, отличное место для еды. Квартире не хватает небольшой террасы.'),
(33, 5, 'es', 'Estancia muy agradable. Puntuación: 9,0. La estancia ha sido muy agradable. El apartamento, limpios y muy cómodo, con vistas impresionantes. Sin duda, recomiendo este alojamiento para una experiencia inolvidable. Es difícil aparcar en determinadas horas del día.'),
(34, 5, 'en', 'Very pleasant stay. Rating: 9.0. The stay was very pleasant. The apartment, clean and very comfortable, with stunning views. I definitely recommend this accommodation for an unforgettable experience. It is difficult to park at certain hours of the day.'),
(35, 5, 'fr', 'Séjour très agréable. Note : 9,0. Le séjour a été très agréable. L\'appartement, propre et très confortable, avec une vue imprenable. Je recommande sans aucun doute ce logement pour une expérience inoubliable. Il est difficile de se garer à certaines heures de la journée.'),
(36, 5, 'cn', '非常愉快的住宿。评分：9,0。住宿非常愉快。公寓干净且非常舒适，视野令人惊叹。我绝对推荐这个住宿，体验难忘。在某些时段停车很困难。'),
(37, 5, 'br', 'Estadia muito agradável. Nota: 9,0. A estadia foi muito agradável. O apartamento, limpo e muito confortável, com vistas deslumbrantes. Definitivamente, recomendo esta acomodação para uma experiência inesquecível. É difícil estacionar em determinados horários do dia.'),
(38, 5, 'it', 'Soggiorno molto piacevole. Voto: 9,0. Il soggiorno è stato molto piacevole. L\'appartamento, pulito e molto comodo, con vista spettacolare. Consiglio assolutamente questa sistemazione per un\'esperienza indimenticabile. È difficile parcheggiare in alcune ore del giorno.'),
(39, 5, 'ua', 'Дуже приємне перебування. Оцінка: 9,0. Перебування було дуже приємним. Квартира, чиста і дуже зручна, з приголомшливим видом. Однозначно рекомендую це житло для незабутнього досвіду. У деякі години дня важко знайти місце для паркування.'),
(40, 5, 'ru', 'Очень приятное пребывание. Оценка: 9,0. Пребывание было очень приятным. Квартира, чистая и очень удобная, с потрясающим видом. Определенно рекомендую это жилье для незабываемого опыта. В некоторых часы дня сложно найти место для парковки.'),
(41, 6, 'cs', 'Fantástico. Puntuación: 9,0. Great view from the apartment. Communication with the owner, at our request we were already accommodated at 12:00. General appearance of the house in which the apartment was located.'),
(42, 6, 'en', 'Fantastic. Rating: 9.0. Great view from the apartment. Communication with the owner, at our request we were already accommodated at 12:00. General appearance of the house in which the apartment was located.'),
(43, 6, 'fr', 'Fantastique. Note : 9,0. Belle vue depuis l\'appartement. Communication avec le propriétaire, à notre demande, nous avons été accueillis dès 12h00. Apparence générale de la maison dans laquelle se trouvait l\'appartement.'),
(44, 6, 'cn', '很棒。评分：9,0。公寓的视野很棒。与房东沟通，我们在要求下已于12:00入住。公寓所在房屋的一般外观。'),
(45, 6, 'br', 'Fantástico. Nota: 9,0. Ótima vista do apartamento. Comunicação com o proprietário, a nosso pedido, fomos acomodados já às 12:00. Aparência geral da casa onde o apartamento estava localizado.'),
(46, 6, 'it', 'Fantastico. Voto: 9,0. Vista fantastica dall\'appartamento. Comunicazione con il proprietario, a nostra richiesta siamo stati già sistemati alle 12:00. Aspetto generale della casa in cui si trovava l\'appartamento.'),
(47, 6, 'ua', 'Фантастично. Оцінка: 9,0. Чудовий вид з квартири. Спілкування з власником, за нашим запитом ми вже були заселені о 12:00. Загальний вигляд будинку, в якому розташована квартира.'),
(48, 6, 'ru', 'Фантастика. Оценка: 9,0. Отличный вид из квартиры. Общение с владельцем, по нашей просьбе нас уже поселили в 12:00. Общий вид дома, в котором находилась квартира.'),
(49, 7, 'en', 'Lovely and would use again. Rating: 10. Very clean, good location, everything you need. Clean and well kept.'),
(50, 7, 'es', 'Encantador y lo usaría de nuevo. Puntuación: 10. Muy limpio, buena ubicación, todo lo que necesitas. Limpio y bien cuidado.'),
(51, 7, 'fr', 'Charmant et je l\'utiliserais à nouveau. Note : 10. Très propre, bon emplacement, tout ce dont vous avez besoin. Propre et bien entretenu.'),
(52, 7, 'cn', '可爱，会再次使用。评分：10。非常干净，位置好，拥有所需的一切。干净且保养良好。'),
(53, 7, 'br', 'Adorável e usaria novamente. Nota: 10. Muito limpo, boa localização, tudo o que você precisa. Limpo e bem cuidado.'),
(54, 7, 'it', 'Delizioso e lo userei di nuovo. Voto: 10. Molto pulito, buona posizione, tutto ciò di cui hai bisogno. Pulito e ben tenuto.'),
(55, 7, 'ua', 'Чудово і я б скористався знову. Оцінка: 10. Дуже чисто, хороше місце розташування, все, що потрібно. Чисто і добре доглянуто.'),
(56, 7, 'ru', 'Прекрасно, и я бы снова воспользовался. Оценка: 10. Очень чисто, хорошее местоположение, всё необходимое. Чисто и ухоженно.'),
(57, 8, 'en', 'Exceptional. Rating: 10. Fantastic modern clean apartment. The air conditioning was working particularly well.'),
(58, 8, 'es', 'Excepcional. Puntuación: 10. Apartamento moderno, limpio y fantástico. El aire acondicionado funcionaba particularmente bien.'),
(59, 8, 'fr', 'Exceptionnel. Note : 10. Appartement moderne, propre et fantastique. La climatisation fonctionnait particulièrement bien.'),
(60, 8, 'cn', '卓越。评分：10。现代化的干净公寓。空调特别好用。'),
(61, 8, 'br', 'Excepcional. Nota: 10. Apartamento moderno, limpo e fantástico. O ar condicionado estava funcionando particularmente bem.'),
(62, 8, 'it', 'Eccezionale. Voto: 10. Appartamento moderno, pulito e fantastico. L\'aria condizionata funzionava particolarmente bene.'),
(63, 8, 'ua', 'Видатно. Оцінка: 10. Сучасна, чиста, фантастична квартира. Кондиціонер працював особливо добре.'),
(64, 8, 'ru', 'Исключительно. Оценка: 10. Современная, чистая и фантастическая квартира. Кондиционер работал особенно хорошо.'),
(65, 9, 'es', 'Super apartamento. Puntuación: 10. Apartamento precioso. Muy buen contacto con la propietaria, vista impresionante. En el apartamento había todo lo necesario.'),
(66, 9, 'en', 'super apartment. Rating: 10. Beautiful apartment. very nice contact with the owner, amazing view. the apartment had everything needed.'),
(67, 9, 'fr', 'super appartement. Note : 10. Bel appartement. très bon contact avec la propriétaire, vue magnifique. L\'appartement avait tout le nécessaire.'),
(68, 9, 'cn', '超级公寓。评分：10。漂亮的公寓。与房东的联系非常好，景色令人惊叹。公寓里有一切所需。'),
(69, 9, 'br', 'super apartamento. Nota: 10. Apartamento bonito. contato muito bom com a proprietária, vista impressionante. No apartamento, havia tudo o que era necessário.'),
(70, 9, 'it', 'super appartamento. Voto: 10. Bellissimo appartamento. ottimo contatto con la proprietaria, vista mozzafiato. Nell\'appartamento c\'era tutto il necessario.'),
(71, 9, 'ua', 'супер апартамент. Оцінка: 10. Чудова квартира. дуже хороший контакт з власницею, приголомшливий вид. У квартирі було все необхідне.'),
(72, 9, 'ru', 'супер апартамент. Оценка: 10. Прекрасная квартира. очень хороший контакт с хозяйкой, потрясающий вид. В квартире было все необходимое.'),
(73, 10, 'es', 'Hermosa vista al mar, pero el calor intenso de la habitación es un inconveniente. Puntuación: 8,0. El apartamento es muy bonito, está en el 9° piso, por lo que tiene una hermosa vista al mar, y está equipado con todo lo necesario para la estancia. La piscina comunitaria también es agradable. Las ventanas en el balcón calientan mucho todo el apartamento durante todo el día hasta que se pone el sol, y los acondicionadores de aire, desafortunadamente, no enfrían lo suficiente. La lavadora que no funcionaba fue un problema.'),
(74, 10, 'en', 'Beautiful sea view, but the intense heat of the room is a drawback. Rating: 8.0. The apartment is very nice, on the 9th floor, so it has a beautiful sea view, and it is equipped with everything needed for the stay. The communal pool is also nice. The windows on the balcony heat up the whole apartment all day until the sun sets, and the air conditioners unfortunately do not cool enough. The non-working washing machine was a problem.'),
(75, 10, 'fr', 'Belle vue sur la mer, mais la chaleur intense de la chambre est un inconvénient. Note : 8,0. L\'appartement est très joli, situé au 9ème étage, il offre une belle vue sur la mer et est équipé de tout le nécessaire pour le séjour. La piscine commune est également agréable. Les fenêtres sur le balcon réchauffent beaucoup tout l\'appartement pendant toute la journée jusqu\'au coucher du soleil, et les climatiseurs, malheureusement, ne refroidissent pas assez. La machine à laver qui ne fonctionnait pas a été un problème.'),
(76, 10, 'cn', '美丽的海景，但房间的强烈热量是一个缺点。评分：8.0。公寓非常漂亮，位于9楼，因此有美丽的海景，并且配备了入住所需的一切。公共游泳池也很好。阳台上的窗户在整个白天将整个公寓加热，直到太阳下山，而空调不幸的是不能很好地冷却。洗衣机不工作的情况是一个问题。'),
(77, 10, 'br', 'Vista maravilhosa do mar, mas o calor intenso do quarto é um problema. Nota: 8,0. O apartamento é muito bonito, está no 9º andar, então tem uma bela vista para o mar e está equipado com tudo o que é necessário para a estadia. A piscina comunitária também é agradável. As janelas na varanda aquecem muito todo o apartamento durante todo o dia até o pôr do sol, e os condicionadores de ar, infelizmente, não refrigeram o suficiente. A máquina de lavar que não funcionava foi um problema.'),
(78, 10, 'it', 'Bella vista sul mare, peccato per il calore intenso della stanza. Voto: 8,0. L\'appartamento è molto carino, è al 9° piano, quindi ha una bella vista sul mare ed è dotato di tutto il necessario per il soggiorno. Anche la piscina condominiale è piacevole. Le vetrate sul balcone riscaldano molto l\'appartamento per tutto il giorno fino al tramonto e i condizionatori di aria, sfortunatamente, non raffreddano abbastanza. La lavatrice non funzionante è stata un problema.'),
(79, 10, 'ua', 'Чудовий вид на море, але інтенсивне тепло кімнати є недоліком. Оцінка: 8,0. Квартира дуже гарна, на 9-му поверсі, тому має чудовий вид на море і обладнана всім необхідним для проживання. Спільний басейн також приємний. Вікна на балконі сильно нагрівають всю квартиру протягом усього дня до заходу сонця, і кондиціонери, на жаль, недостатньо охолоджують. Не працююча пральна машина стала проблемою.'),
(80, 10, 'ru', 'Прекрасный вид на море, но сильная жара в комнате - это недостаток. Оценка: 8,0. Квартира очень симпатичная, находится на 9-м этаже, поэтому имеет красивый вид на море, и оборудована всем необходимым для проживания. Общий бассейн также приятен. Окна на балконе сильно нагревают всю квартиру в течение всего дня до заката солнца, и кондиционеры, к сожалению, не охлаждают достаточно. Неисправная стиральная машина была проблемой.'),
(81, 11, 'es', 'Nos gustó todo. Además, la anfitriona nos proporcionó una cafetera por iniciativa propia, aunque no estaba en los servicios. Hubo un pequeño problema con el internet, pero se solucionó bastante rápido.'),
(82, 11, 'en', 'We liked everything. Additionally, the hostess provided us with a coffee machine on her own initiative, even though it was not included in the services. There was a small problem with the internet, but it was resolved fairly quickly.'),
(83, 11, 'fr', 'Nous avons tout aimé. De plus, l\'hôtesse nous a fourni une machine à café de sa propre initiative, bien qu\'elle ne faisait pas partie des services. Il y a eu un petit problème avec l\'internet, mais il a été résolu assez rapidement.'),
(84, 11, 'cn', '我们喜欢一切。此外，房东主动给我们提供了一台咖啡机，尽管这不在服务范围内。互联网有一个小问题，但很快就解决了。'),
(85, 11, 'br', 'Gostamos de tudo. Além disso, a anfitriã nos forneceu uma máquina de café por iniciativa própria, embora não estivesse nos serviços. Houve um pequeno problema com a internet, mas foi resolvido rapidamente.'),
(86, 11, 'it', 'Ci è piaciuto tutto. Inoltre, l\'host ci ha fornito una macchina da caffè per propria iniziativa, anche se non era inclusa nei servizi. C\'è stato un piccolo problema con Internet, ma è stato risolto abbastanza rapidamente.'),
(87, 11, 'ua', 'Нам сподобалося все. Крім того, господиня за власною ініціативою надала нам кавоварку, хоча вона не входила в послуги. Було невелике питання з інтернетом, але воно було вирішене досить швидко.'),
(88, 11, 'ru', 'Нам все понравилось. В том числе, хозяйка предоставила нам по собственной инициативе кофемашину, хотя в услугах ее не было. Была не большая проблема с интернетом, но она решилась достаточно быстро.'),
(89, 12, 'es', 'Excepcional'),
(90, 12, 'en', 'Exceptional'),
(91, 12, 'fr', 'Exceptionnel'),
(92, 12, 'cn', '卓越'),
(93, 12, 'br', 'Excepcional'),
(94, 12, 'it', 'Eccezionale'),
(95, 12, 'ua', 'Винятковий'),
(96, 12, 'ru', 'Исключительный'),
(97, 13, 'es', 'Excepcional'),
(98, 13, 'en', 'Exceptional'),
(99, 13, 'fr', 'Exceptionnel'),
(100, 13, 'cn', '卓越'),
(101, 13, 'br', 'Excepcional'),
(102, 13, 'it', 'Eccezionale'),
(103, 13, 'ua', 'Винятковий'),
(104, 13, 'ru', 'Исключительный'),
(105, 14, 'es', 'Excepcional'),
(106, 14, 'en', 'Exceptional'),
(107, 14, 'fr', 'Exceptionnel'),
(108, 14, 'cn', '卓越'),
(109, 14, 'br', 'Excepcional'),
(110, 14, 'it', 'Eccezionale'),
(111, 14, 'ua', 'Винятковий'),
(112, 14, 'ru', 'Исключительный'),
(113, 15, 'es', 'Fantástico'),
(114, 15, 'en', 'Fantastic'),
(115, 15, 'fr', 'Fantastique'),
(116, 15, 'cn', '精彩'),
(117, 15, 'br', 'Fantástico'),
(118, 15, 'it', 'Fantastico'),
(119, 15, 'ua', 'Фантастично'),
(120, 15, 'ru', 'Фантастический');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `service_description`, `created_at`, `updated_at`) VALUES
(1, 'Habitación', 'Cama doble con dos mesitas de noche, vistas al mar y un entorno cómodo.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(2, 'Baño', 'Baño privado con ducha y WC, diseñado para tu confort.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(3, 'Salón y Cocina', 'Salón espacioso con sofá cama, mesa y cocina totalmente equipada.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(4, 'Vistas al Mar', 'Disfruta de unas vistas espectaculares al mar desde el salón y la habitación.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(5, 'Lavadora y Secadora', 'Equipado con lavadora y secadora para mantener tu ropa limpia.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(6, 'Extras de Cocina', 'Máquina de café con café y chocolate gratis, y cajones con utensilios de cocina.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(7, 'Puerta Electrónica', 'Puerta con cerradura electrónica para una seguridad sin complicaciones.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(8, 'Otros Servicios', 'Aspiradora para una limpieza rápida y fácil, y otros servicios.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(9, 'Piscina Pública', 'Acceso a una piscina pública cercana, ideal para refrescarse.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(10, 'Wi-Fi Gratuito', 'Conexión Wi-Fi gratuita en el apartamento para que estés conectado.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(11, 'Restaurante', 'A solo 50 metros del apartamento encontrarás un restaurante acogedor.', '2024-11-29 11:41:33', '2024-11-29 11:41:33'),
(12, 'Playa a Pocos Pasos', 'Disfruta de la playa justo en la puerta del apartamento.', '2024-11-29 11:41:33', '2024-11-29 11:41:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services_translations`
--

CREATE TABLE `services_translations` (
  `translation_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `language_code` varchar(5) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `services_translations`
--

INSERT INTO `services_translations` (`translation_id`, `service_id`, `language_code`, `service_name`, `service_description`, `created_at`, `updated_at`) VALUES
(1, 1, 'es', 'Habitación', 'Cama doble con dos mesitas de noche, vistas al mar y un entorno cómodo.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(2, 2, 'es', 'Baño', 'Baño privado con ducha y WC, diseñado para tu confort.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(3, 3, 'es', 'Salón y Cocina', 'Salón espacioso con sofá cama, mesa y cocina totalmente equipada.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(4, 4, 'es', 'Vistas al Mar', 'Disfruta de unas vistas espectaculares al mar desde el salón y la habitación.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(5, 5, 'es', 'Lavadora y Secadora', 'Equipado con lavadora y secadora para mantener tu ropa limpia.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(6, 6, 'es', 'Extras de Cocina', 'Máquina de café con café y chocolate gratis, y cajones con utensilios de cocina.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(7, 7, 'es', 'Puerta Electrónica', 'Puerta con cerradura electrónica para una seguridad sin complicaciones.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(8, 8, 'es', 'Otros Servicios', 'Aspiradora para una limpieza rápida y fácil, y otros servicios.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(9, 9, 'es', 'Piscina Pública', 'Acceso a una piscina pública cercana, ideal para refrescarse.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(10, 10, 'es', 'Wi-Fi Gratuito', 'Conexión Wi-Fi gratuita en el apartamento para que estés conectado.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(11, 11, 'es', 'Restaurante', 'A solo 50 metros del apartamento encontrarás un restaurante acogedor.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(12, 12, 'es', 'Playa a Pocos Pasos', 'Disfruta de la playa justo en la puerta del apartamento.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(13, 1, 'en', 'Bedroom', 'Double bed with two nightstands, sea views and a comfortable setting.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(14, 2, 'en', 'Bathroom', 'Private bathroom with shower and toilet, designed for your comfort.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(15, 3, 'en', 'Living Room and Kitchen', 'Spacious living room with sofa bed, table and fully equipped kitchen.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(16, 4, 'en', 'Sea View', 'Enjoy stunning sea views from the living room and bedroom.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(17, 5, 'en', 'Washer and Dryer', 'Equipped with a washer and dryer to keep your clothes clean.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(18, 6, 'en', 'Kitchen Extras', 'Coffee machine with free coffee and chocolate, and drawers with kitchen utensils.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(19, 7, 'en', 'Electronic Door', 'Door with electronic lock for hassle-free security.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(20, 8, 'en', 'Other Services', 'Vacuum cleaner for quick and easy cleaning, and other services.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(21, 9, 'en', 'Public Pool', 'Access to a nearby public pool, perfect for cooling off.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(22, 10, 'en', 'Free Wi-Fi', 'Free Wi-Fi in the apartment to stay connected.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(23, 11, 'en', 'Restaurant', 'Just 50 meters from the apartment you will find a cozy restaurant.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(24, 12, 'en', 'Beach Steps Away', 'Enjoy the beach right at the doorstep of the apartment.', '2024-11-29 11:41:45', '2024-11-29 11:41:45'),
(25, 1, 'fr', 'Chambre', 'Lit double avec deux tables de chevet, vue sur la mer et cadre confortable.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(26, 2, 'fr', 'Salle de bain', 'Salle de bain privée avec douche et WC, conçue pour votre confort.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(27, 3, 'fr', 'Salon et Cuisine', 'Salon spacieux avec canapé-lit, table et cuisine entièrement équipée.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(28, 4, 'fr', 'Vue sur la Mer', 'Profitez d\'une vue imprenable sur la mer depuis le salon et la chambre.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(29, 5, 'fr', 'Lave-linge et Sèche-linge', 'Équipé d\'une lave-linge et d\'un sèche-linge pour garder vos vêtements propres.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(30, 6, 'fr', 'Extras de Cuisine', 'Machine à café avec café et chocolat gratuits, et tiroirs avec des ustensiles de cuisine.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(31, 7, 'fr', 'Porte Électronique', 'Porte avec serrure électronique pour une sécurité sans tracas.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(32, 8, 'fr', 'Autres Services', 'Aspirateur pour un nettoyage rapide et facile, et autres services.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(33, 9, 'fr', 'Piscine Publique', 'Accès à une piscine publique à proximité, idéale pour se rafraîchir.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(34, 10, 'fr', 'Wi-Fi Gratuit', 'Wi-Fi gratuit dans l\'appartement pour rester connecté.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(35, 11, 'fr', 'Restaurant', 'À seulement 50 mètres de l\'appartement, vous trouverez un restaurant confortable.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(36, 12, 'fr', 'Plage à Deux Pas', 'Profitez de la plage juste devant l\'appartement.', '2024-11-29 11:42:39', '2024-11-29 11:42:39'),
(37, 1, 'it', 'Camera da letto', 'Letto matrimoniale con due comodini, vista mare e un ambiente confortevole.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(38, 2, 'it', 'Bagno', 'Bagno privato con doccia e WC, progettato per il tuo comfort.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(39, 3, 'it', 'Soggiorno e Cucina', 'Soggiorno spazioso con divano letto, tavolo e cucina completamente attrezzata.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(40, 4, 'it', 'Vista Mare', 'Goditi una vista mozzafiato sul mare dal soggiorno e dalla camera da letto.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(41, 5, 'it', 'Lavatrice e Asciugatrice', 'Dotato di lavatrice e asciugatrice per mantenere i vestiti puliti.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(42, 6, 'it', 'Extra in Cucina', 'Macchina per il caffè con caffè e cioccolato gratuiti, e cassetti con utensili da cucina.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(43, 7, 'it', 'Porta Elettronica', 'Porta con serratura elettronica per una sicurezza senza problemi.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(44, 8, 'it', 'Altri Servizi', 'Aspirapolvere per una pulizia rapida e facile, e altri servizi.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(45, 9, 'it', 'Piscina Pubblica', 'Accesso a una piscina pubblica nelle vicinanze, ideale per rinfrescarsi.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(46, 10, 'it', 'Wi-Fi Gratuito', 'Wi-Fi gratuito nell\'appartamento per rimanere connessi.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(47, 11, 'it', 'Ristorante', 'A soli 50 metri dall\'appartamento troverai un ristorante accogliente.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(48, 12, 'it', 'Spiaggia a Due Passi', 'Goditi la spiaggia proprio davanti all\'appartamento.', '2024-11-29 11:43:05', '2024-11-29 11:43:05'),
(61, 1, 'cn', '卧室', '双人床配有两张床头柜，海景和舒适的环境。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(62, 2, 'cn', '浴室', '私人浴室，配有淋浴和厕所，为您的舒适而设计。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(63, 3, 'cn', '客厅和厨房', '宽敞的客厅配有沙发床、桌子和全套厨房设备。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(64, 4, 'cn', '海景', '从客厅和卧室享受壮观的海景。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(65, 5, 'cn', '洗衣机和烘干机', '配有洗衣机和烘干机，保持您的衣物清洁。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(66, 6, 'cn', '厨房附加设施', '配有免费咖啡和巧克力的咖啡机，以及带厨房用具的抽屉。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(67, 7, 'cn', '电子门', '带有电子锁的门，提供无忧的安全性。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(68, 8, 'cn', '其他服务', '用于快速和轻松清洁的吸尘器，以及其他服务。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(69, 9, 'cn', '公共泳池', '可前往附近的公共泳池，非常适合消暑。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(70, 10, 'cn', '免费Wi-Fi', '公寓内提供免费的Wi-Fi连接，让您保持联络。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(71, 11, 'cn', '餐厅', '离公寓仅50米，您会找到一家舒适的餐厅。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(72, 12, 'cn', '海滩近在咫尺', '享受就在公寓门口的海滩。', '2024-11-29 11:43:22', '2024-11-29 11:43:22'),
(73, 1, 'br', 'Quarto', 'Cama de casal com duas mesas de cabeceira, vista para o mar e um ambiente confortável.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(74, 2, 'br', 'Banheiro', 'Banheiro privativo com chuveiro e toalete, projetado para seu conforto.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(75, 3, 'br', 'Sala e Cozinha', 'Sala espaçosa com sofá-camas, mesa e cozinha totalmente equipada.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(76, 4, 'br', 'Vista para o Mar', 'Desfrute de vistas deslumbrantes para o mar da sala e do quarto.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(77, 5, 'br', 'Máquina de Lavar e Secar', 'Equipado com máquina de lavar e secar roupa para manter suas roupas limpas.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(78, 6, 'br', 'Extras de Cozinha', 'Máquina de café com café e chocolate grátis, e gavetas com utensílios de cozinha.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(79, 7, 'br', 'Porta Eletrônica', 'Porta com fechadura eletrônica para segurança sem complicações.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(80, 8, 'br', 'Outros Serviços', 'Aspirador para limpeza rápida e fácil, e outros serviços.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(81, 9, 'br', 'Piscina Pública', 'Acesso a uma piscina pública nas proximidades, ideal para se refrescar.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(82, 10, 'br', 'Wi-Fi Grátis', 'Wi-Fi gratuito no apartamento para se manter conectado.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(83, 11, 'br', 'Restaurante', 'Apenas a 50 metros do apartamento, você encontrará um restaurante acolhedor.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(84, 12, 'br', 'Praia a Poucos Passos', 'Desfrute da praia logo na porta do apartamento.', '2024-11-29 11:43:38', '2024-11-29 11:43:38'),
(85, 1, 'ua', 'Спальня', 'Двоспальне ліжко з двома тумбочками, вид на море та комфортне середовище.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(86, 2, 'ua', 'Ванна кімната', 'Приватна ванна кімната з душем та туалетом, створена для вашого комфорту.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(87, 3, 'ua', 'Вітальня та Кухня', 'Простора вітальня з розкладним диваном, столом та повністю обладнаною кухнею.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(88, 4, 'ua', 'Вид на Море', 'Насолоджуйтесь захоплюючим видом на море з вітальні та спальні.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(89, 5, 'ua', 'Пральна машина та Сушарка', 'Оснащена пральною машиною та сушаркою для чистоти вашого одягу.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(90, 6, 'ua', 'Додаткові Послуги Кухні', 'Кавоварка з безкоштовною кавою та шоколадом, а також шухляди з кухонними приладами.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(91, 7, 'ua', 'Електронні Двері', 'Двері з електронним замком для безпечного доступу без клопотів.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(92, 8, 'ua', 'Інші Послуги', 'Пилосос для швидкого та легкого прибирання та інші послуги.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(93, 9, 'ua', 'Громадський Басейн', 'Доступ до громадського басейну поблизу, ідеального для освіження.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(94, 10, 'ua', 'Безкоштовний Wi-Fi', 'Безкоштовний Wi-Fi в апартаментах для підтримки зв\'язку.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(95, 11, 'ua', 'Ресторан', 'Лише за 50 метрів від апартаментів розташований затишний ресторан.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(96, 12, 'ua', 'Пляж У Декількох Кроках', 'Насолоджуйтесь пляжем прямо перед апартаментами.', '2024-11-29 11:43:57', '2024-11-29 11:43:57'),
(97, 1, 'ru', 'Спальня', 'Двуспальная кровать с двумя тумбочками, вид на море и уютная обстановка.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(98, 2, 'ru', 'Ванная комната', 'Частная ванная комната с душем и туалетом, разработанная для вашего комфорта.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(99, 3, 'ru', 'Гостиная и Кухня', 'Просторная гостиная с раскладным диваном, столом и полностью оборудованной кухней.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(100, 4, 'ru', 'Вид на море', 'Наслаждайтесь потрясающим видом на море из гостиной и спальни.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(101, 5, 'ru', 'Стиральная машина и Сушилка', 'Оснащена стиральной машиной и сушилкой, чтобы поддерживать ваши вещи в чистоте.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(102, 6, 'ru', 'Кухонные Дополнения', 'Кофемашина с бесплатным кофе и шоколадом, а также ящики с кухонными принадлежностями.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(103, 7, 'ru', 'Электронный замок', 'Дверь с электронным замком для безопасного и удобного доступа.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(104, 8, 'ru', 'Другие Услуги', 'Пылесос для быстрой и легкой уборки, а также другие услуги.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(105, 9, 'ru', 'Общественный бассейн', 'Доступ к общественному бассейну рядом, идеально подходит для освежения.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(106, 10, 'ru', 'Бесплатный Wi-Fi', 'Бесплатный Wi-Fi в апартаменте для поддержания связи.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(107, 11, 'ru', 'Ресторан', 'Всего в 50 метрах от апартаментов находится уютный ресторан.', '2024-11-29 11:44:42', '2024-11-29 11:44:42'),
(108, 12, 'ru', 'Пляж у дверей', 'Наслаждайтесь пляжем прямо у дверей апартаментов.', '2024-11-29 11:44:42', '2024-11-29 11:44:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token_verificacion` varchar(255) DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT 0,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `token_verificacion`, `verificado`, `fecha_registro`, `role`) VALUES
(181, 'https', '$2y$10$wS54iF16cxOWElyY.GvmIuU4WwfUz054Q9AU4NTsaxOf1C1wBhe.a', NULL, 0, '2024-12-13 15:47:28', 'user'),
(182, 'alvaro', '$2y$10$pR4U.OYMB4XbfJp9xxM0vO5jv7Qu.TtOq9FwN2ogUPaQ0WdWUPtF2', NULL, 0, '2024-12-16 08:25:08', 'admin'),
(183, 'juan', '$2y$10$c7wojnuV23emfNTD7AVtnumPSICSfZ28L0wQ7g4NUuAyXLitoaDZ.', NULL, 0, '2024-12-16 08:27:13', 'user'),
(184, 'Manuel', '$2y$10$ZJcz3XjAgJyIPP3uVSDtK./JtZpB1coiuoNqcEM9JL9WH.bN/gouK', NULL, 0, '2024-12-16 08:28:16', 'admin'),
(185, 'bruce', '$2y$10$2GIDRI4Cp1S.3Fbl0QB8L.W3mGHCjk/9xRfwPEWWHE6Whz1cZ.4yS', NULL, 0, '2024-12-16 08:30:41', 'user'),
(186, 'Bruxter', '$2y$10$ha0Sop/q.rAakcL15oTeqeqxLPvyuWfHD90BPgB/c3ARQFfd5Nyuy', NULL, 0, '2024-12-16 08:31:18', 'user'),
(187, 'espabilasergio', '$2y$10$6OPkpy4VcWSqjY/Jp.vveu.tlvk5.yvjPsyrrRhabrQhzKbXUDsvG', NULL, 0, '2024-12-16 08:31:25', 'user'),
(189, 'pedro', '$2y$10$fvMpMrzfHoxMLHcJ/aECr.SdUZUwxOP0mNT0bkZLFuoa/kH5.RpJm', NULL, 0, '2024-12-16 08:43:04', 'admin'),
(190, 'sergio', '$2y$10$Ri90jgzsW.O1qYZHYJzHa.s3VxzZQeLvEyAreJghbsu4pqPyIAhQm', NULL, 0, '2024-12-16 08:53:51', 'admin'),
(191, 'Gerard', '$2y$10$WKdz9909qy/mSsT.xuJHlecg7iXSMBfqGN3w2vGnrX8UoFiFPJMQG', NULL, 0, '2025-01-16 21:35:33', 'user');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indices de la tabla `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indices de la tabla `review_translations`
--
ALTER TABLE `review_translations`
  ADD PRIMARY KEY (`translation_id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indices de la tabla `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indices de la tabla `services_translations`
--
ALTER TABLE `services_translations`
  ADD PRIMARY KEY (`translation_id`),
  ADD UNIQUE KEY `unique_translation` (`service_id`,`language_code`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `password` (`password`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contact`
--
ALTER TABLE `contact`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `review_translations`
--
ALTER TABLE `review_translations`
  MODIFY `translation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT de la tabla `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `services_translations`
--
ALTER TABLE `services_translations`
  MODIFY `translation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `review_translations`
--
ALTER TABLE `review_translations`
  ADD CONSTRAINT `review_translations_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`review_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `services_translations`
--
ALTER TABLE `services_translations`
  ADD CONSTRAINT `services_translations_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
