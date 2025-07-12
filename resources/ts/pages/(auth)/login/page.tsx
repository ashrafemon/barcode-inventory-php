import AuthLayout from '~/components/Layouts/Auth';
import LoginForm from '../../../components/LoginForm';

const page = () => {
	return <LoginForm />;
};

page.layout = (page: any) => <AuthLayout children={page} />;
export default page;
