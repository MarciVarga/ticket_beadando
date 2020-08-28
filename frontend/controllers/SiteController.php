<?php
namespace frontend\controllers;

use common\models\Comment;
use common\models\Ticket;
use common\models\User;
use common\models\CommentForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\TicketForm;
use frontend\models\UserForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Response;

use yii\web\UploadedFile;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'show-ticket', 'close-ticket', 'update-profile', 'show-user-tickets', 'show-profile', 'add-ticket'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'show-ticket', 'close-ticket', 'update-profile', 'show-user-tickets', 'show-profile', 'add-ticket'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        $a = 22;
        $b = ["egy", "ketto", "harom"];

        return $this->render('about', [
            'a' => $a,
            'b' => $b,
        ]);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    //new code

    /**
     * Add a Ticket
     *
     * @return string|Response
     */
    public function actionAddTicket()
    {
        $model = new TicketForm();
        if (Yii::$app->request->isPost  && $model->load($_POST)) {
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');

            /*if ($model->validate()) {
                $model->upload();

                if ($model->addTicket()) {
                    Yii::$app->session->setFlash('success', 'Ticket addition was successful.');

                    return $this->goHome();
                }
            }*/

            if ($model->validate()) {
                if ($model->addTicket()) {
                    Yii::$app->session->setFlash('success', 'Ticket addition was successful.');

                    $folder_read = Yii::getAlias('@frontend/web/uploads/');
                    $folder_write = Yii::getAlias('@backend/web/uploads/');
                    $upload_copy = opendir($folder_read);

                    while ($file = readdir($upload_copy)) {
                        if ($file != "." && $file != ".." && $file != ".gitkeep") {
                            copy($folder_read . $file, $folder_write . $file);
                        }
                    }

                    closedir($upload_copy);

                    return $this->goHome();
                } else {
                    Yii::$app->session->setFlash('error', 'Something went wrong.');
                }
            }
        }

        return $this->render('add_ticket', [
            'model' => $model,
        ]);
    }

    /**
     * Displays the logged in user's profile
     *
     * @return string
     */
    public function actionShowProfile()
    {
            $model = User::find()
                ->where(['id' => Yii::$app->user->identity->getId()])
                ->one();

            $data = [
                $model->username,
                $model->email,
                "Created At: " . date("Y-m-d H:i:s", $model->created_at),
                "Last Login: " . $model->last_login,
            ];

            return $this->render('show_profile', [
                'data' => $data,
            ]);
    }

    /**
     * Lists the logged in user's tickets
     *
     * @return string
     */
    public function actionShowUserTickets()
    {
        $tickets = Ticket::find()
            ->where(['user_id'=>Yii::$app->user->identity->getId()])
            ->with('comments')
            ->orderBy([
                'is_open' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all();

        return $this->render('show_user_tickets', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Updates the logged in user's profile
     *
     * @return string
     */
    public function actionUpdateProfile()
    {
        $user = User::find()
            ->ofId(Yii::$app->user->identity->getId())
            ->one();

        $model = new UserForm();
        $model->fillFrom($user);

        if ($model->load(Yii::$app->request->post())) {
            if ( $model->validate() && $user->validatePassword($model->old_password)) {
                $user = $model->fillTo($user);
                $user->save();

                Yii::$app->session->setFlash('success', 'You have updated the profile.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to save');
            }
        }

        return $this->render('update_profile', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    /**
     * Displays the logged in user's ticket
     *
     * @param $id integer ticket's ID
     * @return string|Response
     * @throws Exception
     */
    public function actionShowTicket($id)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::find()
            ->ofId($id)
            ->ofUserId(Yii::$app->user->identity->getId())
            ->one();

        if ($ticket == null) {
            return $this->goHome();
        }

        $transaction = Yii::$app->db->beginTransaction();
        $request = Yii::$app->request->post();
        $commentForm = new CommentForm();


        if ($commentForm->load($request) && $commentForm->validate()) {
            $comment = new Comment();
            $comment = $comment->fillFrom($commentForm);
            $comment->user_id = Yii::$app->user->id;
            $comment->ticket_id = $id;

            if (!$ticket->is_open) {
                $ticket->is_open = true;
            }

            if ($comment->save() && $ticket->save()) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
        }

        $comments = $ticket->comments;

        $images = $ticket->images;

        return $this->render('show_ticket', [
            'comment_form' => $commentForm,
            'ticket' => $ticket,
            'comments' => $comments,
            'images' => $images,
        ]);
    }

    /**
     * Closes a specific ticket
     *
     * @param $id integer the ticket's ID
     * @return false|Response
     */
    public function actionCloseTicket($id)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::find()
            ->ofId($id)
            ->ofUserId(Yii::$app->user->identity->getId())
            ->one();

        if ($ticket == null) {
            return $this->goHome();
        }

        if ($ticket->is_open) {
            $ticket->is_open = false;
        }

        if ($ticket->save()) {
            Yii::$app->session->setFlash('success', 'You have closed this ticket: ' . $ticket->title);

            $this->redirect('/site/show-user-tickets');
        } else {
            Yii::$app->session->setFlash('error', 'Ticket close failed.');

            return $this->goHome();
        }

        return false;
    }
}
