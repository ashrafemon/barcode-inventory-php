import { Icon } from '@iconify-icon/react';
import { Link, router } from '@inertiajs/react';
import { AppShell, Burger, Button, Group, NavLink } from '@mantine/core';
import { useDisclosure } from '@mantine/hooks';
import { PropsWithChildren } from 'react';
import { Navs } from '../../constants/navigations';
import { SITE_URL } from '../../constants/urls';
import useAuth from '../../hooks/useAuth';
import { alertMessage } from '../../utils/helper';

const PanelLayout = ({ children }: PropsWithChildren) => {
	const auth = useAuth();
	const [opened, { toggle }] = useDisclosure();

	const logoutHandler = () => {
		auth.unauthenticated(() => {
			alertMessage({ title: 'Logout successful', icon: 'success' });
			router.visit(SITE_URL.auth.login, { replace: true });
		});
	};

	return (
		<AppShell
			header={{ height: 60 }}
			navbar={{
				width: 200,
				breakpoint: 'sm',
				collapsed: { mobile: !opened },
			}}
			padding="md"
		>
			<AppShell.Header px="sm">
				<Group align="center" justify="space-between">
					<Group gap="xs">
						<Burger
							opened={opened}
							onClick={toggle}
							hiddenFrom="sm"
							size="sm"
						/>
						<Icon
							icon="streamline-ultimate-color:barcode"
							width={60}
						/>
					</Group>

					<Button color="red" onClick={logoutHandler}>
						Logout
					</Button>
				</Group>
			</AppShell.Header>
			<AppShell.Navbar p="md">
				{Navs.map((nav, index) => (
					<NavLink
						key={index}
						label={nav.label}
						component={Link}
						href={nav.href}
					/>
				))}
			</AppShell.Navbar>
			<AppShell.Main>{children}</AppShell.Main>
		</AppShell>
	);
};

export default PanelLayout;
